<?php

namespace App\Controllers;

use App\Models\Cart;
use App\Models\ProductVariant;
use Exception;

class CartController extends BaseController
{
    private $cartModel;
    private $variantModel;

    public function __construct()
    {
        parent::__construct();
        $this->checkRole(['USER']);
        $this->cartModel = new Cart();
        $this->variantModel = new ProductVariant();
    }

    public function index()
    {
        $userId = $this->auth->getUserId();
        $cartItems = $this->cartModel->getCartItems($userId);
        $cartTotal = $this->cartModel->getCartTotal($userId);

        $this->view('cart/index', [
            'title' => 'Giỏ hàng',
            'cartItems' => $cartItems,
            'cartTotal' => $cartTotal
        ]);
    }

    public function add()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Invalid request method');
            }

            $userId = $this->auth->getUserId();
            $variantId = $_POST['variantId'] ?? null;
            $quantity = (int)($_POST['quantity'] ?? 1);

            if (!$variantId) {
                throw new Exception('Vui lòng chọn biến thể sản phẩm');
            }

            // Kiểm tra số lượng tồn kho
            $variant = $this->variantModel->findById($variantId);
            if (!$variant) {
                throw new Exception('Biến thể sản phẩm không tồn tại');
            }

            if ($variant['quantity'] < $quantity) {
                throw new Exception('Số lượng sản phẩm không đủ');
            }

            $this->cartModel->addToCart($userId, $variantId, $quantity);
            
            $this->jsonResponse([
                'success' => true,
                'message' => 'Thêm vào giỏ hàng thành công'
            ]);

        } catch (Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function update()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Invalid request method');
            }

            $userId = $this->auth->getUserId();
            $variantId = $_POST['variantId'] ?? null;
            $quantity = (int)($_POST['quantity'] ?? 0);

            if (!$variantId || $quantity <= 0) {
                throw new Exception('Dữ liệu không hợp lệ');
            }

            // Kiểm tra số lượng tồn kho
            $variant = $this->variantModel->findById($variantId);
            if ($variant['quantity'] < $quantity) {
                throw new Exception('Số lượng sản phẩm không đủ');
            }

            $this->cartModel->updateQuantity($userId, $variantId, $quantity);
            
            // Lấy thông tin giỏ hàng mới
            $cartTotal = $this->cartModel->getCartTotal($userId);
            
            $this->jsonResponse([
                'success' => true,
                'message' => 'Cập nhật giỏ hàng thành công',
                'cartTotal' => $cartTotal
            ]);

        } catch (Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function remove()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Invalid request method');
            }

            $userId = $this->auth->getUserId();
            $variantId = $_POST['variantId'] ?? null;

            if (!$variantId) {
                throw new Exception('Dữ liệu không hợp lệ');
            }

            $this->cartModel->removeFromCart($userId, $variantId);
            
            // Lấy thông tin giỏ hàng mới
            $cartTotal = $this->cartModel->getCartTotal($userId);
            
            $this->jsonResponse([
                'success' => true,
                'message' => 'Xóa sản phẩm thành công',
                'cartTotal' => $cartTotal
            ]);

        } catch (Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function clear()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Invalid request method');
            }

            $userId = $this->auth->getUserId();
            $this->cartModel->clearCart($userId);
            
            $this->jsonResponse([
                'success' => true,
                'message' => 'Xóa giỏ hàng thành công'
            ]);

        } catch (Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function processSelectedItems()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Invalid request method');
            }

            $userId = $this->auth->getUserId();
            $selectedVariantIds = $_POST['variantIds'] ?? [];

            error_log('Selected Variant IDs: ' . print_r($selectedVariantIds, true));

            if (empty($selectedVariantIds)) {
                throw new Exception('Vui lòng chọn sản phẩm');
            }

            // Lấy thông tin sản phẩm đã chọn
            $cartItems = $this->cartModel->getSelectedCartItems($userId, $selectedVariantIds);
            
            error_log('Cart Items: ' . print_r($cartItems, true));

            if (empty($cartItems)) {
                throw new Exception('Không tìm thấy sản phẩm đã chọn');
            }

            // Lưu vào session
            $_SESSION['selected_items'] = $cartItems;
            $_SESSION['selected_variant_ids'] = $selectedVariantIds;

            error_log('Session after save: ' . print_r($_SESSION, true));

            $this->jsonResponse([
                'success' => true,
                'message' => 'Đã chọn sản phẩm thành công'
            ]);

        } catch (Exception $e) {
            error_log('Process Selected Items Error: ' . $e->getMessage());
            $this->jsonResponse([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
} 