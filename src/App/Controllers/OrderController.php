<?php

namespace App\Controllers;

use App\Models\Order;
use App\Models\Cart;
use App\Models\Address;
use App\Models\ProductVariant;
use Core\Database;
use Exception;

class OrderController extends BaseController
{
    private $orderModel;
    private $cartModel;
    private $addressModel;
    private $variantModel;
    private $db;

    public function __construct()
    {
        parent::__construct();
        $this->checkRole(['USER']);
        $this->orderModel = new Order();
        $this->cartModel = new Cart();
        $this->addressModel = new Address();
        $this->db = Database::getInstance()->getConnection();
    }

    public function checkout()
    {
        try {
            // 1. Kiểm tra user đã đăng nhập
            $userId = $this->auth->getUserId();
            if (!$userId) {
                $_SESSION['error'] = 'Vui lòng đăng nhập để tiếp tục';
                $this->redirect('login');
                return;
            }

            // 2. Lấy danh sách variantId được chọn
            $selectedVariantIds = $_POST['selectedItems'] ?? [];
            if (empty($selectedVariantIds)) {
                $_SESSION['error'] = 'Vui lòng chọn sản phẩm để mua';
                $this->redirect('cart');
                return;
            }

            // 3. Lấy thông tin giỏ hàng cho các sản phẩm được chọn
            $cartItems = $this->cartModel->getSelectedCartItems($userId, $selectedVariantIds);
            if (empty($cartItems)) {
                $_SESSION['error'] = 'Không tìm thấy sản phẩm đã chọn';
                $this->redirect('cart');
                return;
            }

            // 4. Kiểm tra số lượng tồn kho
            foreach ($cartItems as $item) {
                if ($item['quantity'] > $item['stockQuantity']) {
                    $_SESSION['error'] = "Sản phẩm {$item['productName']} chỉ còn {$item['stockQuantity']} trong kho";
                    $this->redirect('cart');
                    return;
                }
            }

            // 5. Lấy địa chỉ của user
            $addresses = $this->addressModel->findByUserId($userId);
            if (empty($addresses)) {
                $_SESSION['no_address'] = true;
                $this->redirect('cart');
                return;
            }

            // 6. Tính tổng tiền các sản phẩm được chọn
            $cartTotal = array_reduce($cartItems, function ($total, $item) {
                return $total + ($item['finalPrice'] * $item['quantity']);
            }, 0);

            // 7. Render view với dữ liệu
            $this->view('order/checkout', [
                'title' => 'Thanh toán đơn hàng',
                'cartItems' => $cartItems,
                'addresses' => $addresses,
                'cartTotal' => $cartTotal,
                'selectedVariantIds' => $selectedVariantIds
            ]);

        } catch (Exception $e) {
            error_log('Checkout Error: ' . $e->getMessage());
            $_SESSION['error'] = 'Có lỗi xảy ra: ' . $e->getMessage();
            $this->redirect('cart');
        }
    }

    public function create()
    {
        try {
            // Bắt đầu transaction
            $this->db->beginTransaction();

            $userId = $this->auth->getUserId();
            if (!$userId) {
                throw new Exception('Bạn cần đăng nhập để tạo đơn hàng');
            }

            $addressId = $_POST['addressId'] ?? null;
            $paymentMethod = $_POST['paymentMethod'] ?? null;
            $note = $_POST['note'] ?? '';

            // Validate input
            if (!$addressId || !$paymentMethod) {
                throw new Exception('Vui lòng điền đầy đủ thông tin');
            }

            // Lấy và kiểm tra giỏ hàng
            $cartItems = $this->cartModel->getCartItems($userId);
            if (empty($cartItems)) {
                throw new Exception('Giỏ hàng trống');
            }

            // Kiểm tra lại số lượng tồn kho
            foreach ($cartItems as $item) {
                if ($item['quantity'] > $item['stockQuantity']) {
                    throw new Exception("Sản phẩm {$item['productName']} chỉ còn {$item['stockQuantity']} trong kho");
                }
            }

            // Tạo đơn hàng
            $orderData = [
                'userId' => $userId,
                'addressId' => $addressId,
                'totalAmount' => array_sum(array_map(function ($item) {
                    return $item['finalPrice'] * $item['quantity'];
                }, $cartItems)),
                'paymentMethod' => $paymentMethod,
                'note' => $note,
                'items' => array_map(function ($item) {
                    return [
                        'variantId' => $item['variantId'],
                        'quantity' => $item['quantity'],
                        'price' => $item['finalPrice']
                    ];
                }, $cartItems)
            ];

            // Lưu đơn hàng
            $orderId = $this->orderModel->createOrder($orderData);
            if (!$orderId) {
                throw new Exception('Không thể tạo đơn hàng');
            }

            // Cập nhật số lượng tồn kho
            foreach ($cartItems as $item) {
                $this->variantModel->updateStock(
                    $item['variantId'],
                    $item['stockQuantity'] - $item['quantity']
                );
            }

            // Xóa giỏ hàng
            $this->cartModel->clearCart($userId);

            // Commit transaction
            $this->db->commit();

            // Redirect theo phương thức thanh toán
            if ($paymentMethod === 'BANKING') {
                $this->redirect("payment/banking/{$orderId}");
            } else {
                $this->redirect("order/success/{$orderId}");
            }

        } catch (Exception $e) {
            // Rollback nếu có lỗi
            $this->db->rollBack();

            $_SESSION['error'] = $e->getMessage();
            $this->redirect('checkout');
        }
    }

    public function payment($orderId)
    {
        $userId = $this->auth->getUserId();
        $order = $this->orderModel->getOrderDetails($orderId);

        if (!$order || $order['userId'] !== $userId) {
            $this->redirect('404');
            return;
        }

        $this->view('order/payment', [
            'title' => 'Thanh toán',
            'order' => $order
        ]);
    }

    public function uploadPayment($orderId)
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Invalid request method');
            }

            $userId = $this->auth->getUserId();
            $order = $this->orderModel->getOrderDetails($orderId);

            if (!$order || $order['userId'] !== $userId) {
                throw new Exception('Đơn hàng không tồn tại');
            }

            // Xử lý upload ảnh
            if (!isset($_FILES['bankingImage']) || $_FILES['bankingImage']['error'] !== UPLOAD_ERR_OK) {
                throw new Exception('Vui lòng chọn ảnh chuyển khoản');
            }

            $file = $_FILES['bankingImage'];
            $fileName = uniqid() . '_' . $file['name'];
            $uploadPath = ROOT_PATH . '/public/uploads/payments/' . $fileName;

            if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
                throw new Exception('Không thể upload ảnh');
            }

            // Cập nhật đường dẫn ảnh
            $this->orderModel->updateBankingImage($orderId, '/uploads/payments/' . $fileName);

            $this->redirect("order/success/{$orderId}");

        } catch (Exception $e) {
            $this->view('order/payment', [
                'error' => $e->getMessage(),
                'order' => $order
            ]);
        }
    }

    public function success($orderId)
    {
        $userId = $this->auth->getUserId();
        $order = $this->orderModel->getOrderDetails($orderId);

        if (!$order || $order['userId'] !== $userId) {
            $this->redirect('404');
            return;
        }

        $this->view('order/success', [
            'title' => 'Đặt hàng thành công',
            'order' => $order
        ]);
    }

    public function history()
    {
        $userId = $this->auth->getUserId();
        $orders = $this->orderModel->getOrdersByUser($userId);

        $this->view('order/history', [
            'title' => 'Lịch sử đơn hàng',
            'orders' => $orders
        ]);
    }

    public function detail($orderId)
    {
        $userId = $this->auth->getUserId();
        $order = $this->orderModel->getOrderDetails($orderId);

        if (!$order || $order['userId'] !== $userId) {
            $this->redirect('404');
            return;
        }

        $this->view('order/detail', [
            'title' => 'Chi tiết đơn hàng #' . $orderId,
            'order' => $order
        ]);
    }

    public function cancel($orderId)
    {
        try {
            $userId = $this->auth->getUserId();
            $order = $this->orderModel->getOrderDetails($orderId);

            if (!$order || $order['userId'] !== $userId) {
                throw new Exception('Đơn hàng không tồn tại');
            }

            if ($order['orderStatus'] !== 'PENDING') {
                throw new Exception('Không thể hủy đơn hàng này');
            }

            $this->orderModel->updateOrderStatus($orderId, 'CANCELLED', 'Khách hàng hủy đơn', $userId);

            $this->redirect("order/detail/{$orderId}");

        } catch (Exception $e) {
            $this->view('order/detail', [
                'error' => $e->getMessage(),
                'order' => $order
            ]);
        }
    }
}