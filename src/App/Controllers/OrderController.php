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
        $this->variantModel = new ProductVariant();
        $this->db = Database::getInstance()->getConnection();
    }

    public function checkout()
{
    try {
        error_log('=== START CHECKOUT ===');
        
        // 1. Kiểm tra user đã đăng nhập
        $userId = $this->auth->getUserId();
        if (!$userId) {
            $_SESSION['error'] = 'Vui lòng đăng nhập để tiếp tục';
            $this->redirect('login');
            return;
        }

        // 2. Lấy danh sách variantId được chọn từ POST request
        $selectedVariantIds = isset($_POST['selectedItems']) ? $_POST['selectedItems'] : [];
        error_log('Selected Variant IDs from POST: ' . print_r($selectedVariantIds, true));

        if (empty($selectedVariantIds)) {
            $_SESSION['error'] = 'Vui lòng chọn sản phẩm để mua';
            $this->redirect('cart');
            return;
        }

        // 3. Lấy thông tin giỏ hàng cho các sản phẩm được chọn
        $cartItems = $this->cartModel->getSelectedCartItems($userId, $selectedVariantIds);
        error_log('Cart Items: ' . print_r($cartItems, true));

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
            $_SESSION['error'] = 'Vui lòng th��m địa chỉ giao hàng';
            $this->redirect('address/create');
            return;
        }

        // 6. Tính tổng tiền
        $cartTotal = array_reduce($cartItems, function ($total, $item) {
            return $total + ($item['finalPrice'] * $item['quantity']);
        }, 0);

        // 7. Lưu thông tin vào session để sử dụng ở bước tạo đơn hàng
        $_SESSION['selected_items'] = $cartItems;
        $_SESSION['selected_variant_ids'] = $selectedVariantIds;

        error_log('Session after save: ' . print_r($_SESSION, true));

        // 8. Render view checkout
        $this->view('order/checkout', [
            'title' => 'Thanh toán đơn hàng',
            'cartItems' => $cartItems,
            'addresses' => $addresses,
            'cartTotal' => $cartTotal,
            'selectedVariantIds' => $selectedVariantIds
        ]);

    } catch (Exception $e) {
        error_log('Checkout Error: ' . $e->getMessage());
        $_SESSION['error'] = $e->getMessage();
        $this->redirect('cart');
    }
}

    public function create()
    {
        try {
            error_log('=== START CREATE ORDER ===');
            error_log('POST Data: ' . print_r($_POST, true));
            error_log('Session Data: ' . print_r($_SESSION, true));

            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Invalid request method');
            }

            $userId = $this->auth->getUserId();
            if (!$userId) {
                throw new Exception('Vui lòng đăng nhập để tiếp tục');
            }

            // Lấy dữ liệu từ form và session
            $addressId = filter_input(INPUT_POST, 'addressId', FILTER_VALIDATE_INT);
            $paymentMethod = filter_input(INPUT_POST, 'paymentMethod', FILTER_SANITIZE_STRING);
            $cartItems = $_SESSION['selected_items'] ?? [];
            $selectedVariantIds = $_SESSION['selected_variant_ids'] ?? [];

            error_log('Address ID: ' . $addressId);
            error_log('Payment Method: ' . $paymentMethod);
            error_log('Cart Items: ' . print_r($cartItems, true));
            error_log('Selected Variant IDs: ' . print_r($selectedVariantIds, true));

            // Validate dữ liệu
            if (!$addressId) {
                throw new Exception('Vui lòng chọn địa chỉ giao hàng');
            }
            if (!$paymentMethod) {
                throw new Exception('Vui lòng chọn phương thức thanh toán');
            }
            if (empty($cartItems)) {
                throw new Exception('Không có sản phẩm nào được chọn');
            }

            // Tính tổng tiền
            $totalAmount = array_reduce($cartItems, function ($total, $item) {
                return $total + ($item['finalPrice'] * $item['quantity']);
            }, 0);

            $this->db->beginTransaction();

            try {
                // 1. Tạo đơn hàng
                $orderData = [
                    'userId' => $userId,
                    'addressId' => $addressId,
                    'totalAmount' => $totalAmount,
                    'paymentMethod' => $paymentMethod,
                    'orderStatus' => 'PENDING',
                    'paymentStatus' => 'PENDING'
                ];
                
                $orderId = $this->orderModel->create($orderData);
                if (!$orderId) {
                    throw new Exception('Không thể tạo đơn hàng');
                }

                // 2. Tạo chi tiết đơn hàng
                foreach ($cartItems as $item) {
                    $orderItemData = [
                        'orderId' => $orderId,
                        'variantId' => $item['variantId'],
                        'quantity' => $item['quantity'],
                        'price' => $item['finalPrice']
                    ];
                    
                    if (!$this->orderModel->createOrderItem($orderItemData)) {
                        throw new Exception('Không thể tạo chi tiết đơn hàng');
                    }
                }

                // 3. Tạo bản ghi thanh toán
                $paymentData = [
                    'orderId' => $orderId,
                    'amount' => $totalAmount,
                    'paymentMethod' => $paymentMethod,
                    'paymentStatus' => 'PENDING'
                ];
                
                if (!$this->orderModel->createPayment($paymentData)) {
                    throw new Exception('Không thể tạo bản ghi thanh toán');
                }

                // 4. Tạo lịch sử đơn hàng
                $orderHistoryData = [
                    'orderId' => $orderId,
                    'status' => 'PENDING',
                    'note' => 'Đơn hàng mới được tạo',
                    'createdBy' => $userId
                ];
                
                if (!$this->orderModel->createOrderHistory($orderHistoryData)) {
                    throw new Exception('Không thể tạo lịch sử đơn hàng');
                }

                // 5. Cập nhật số lượng tồn kho và xóa giỏ hàng
                foreach ($cartItems as $item) {
                    // Cập nhật số lượng tồn kho
                    $newQuantity = $item['stockQuantity'] - $item['quantity'];
                    if (!$this->variantModel->updateStock($item['variantId'], $newQuantity)) {
                        throw new Exception('Không thể cập nhật số lượng tồn kho');
                    }
                    // Xóa khỏi giỏ hàng
                    $this->cartModel->removeFromCart($userId, $item['variantId']);
                }

                // 6. Xóa session
                unset($_SESSION['selected_items']);
                unset($_SESSION['selected_variant_ids']);

                $this->db->commit();
                error_log('Transaction committed successfully');

                // 7. Chuyển hướng
                if ($paymentMethod === 'QR_TRANSFER') {
                    $this->redirect("order/payment/{$orderId}");
                } else {
                    $this->redirect("order/success/{$orderId}");
                }

            } catch (Exception $e) {
                $this->db->rollBack();
                error_log('Transaction Error: ' . $e->getMessage());
                throw $e;
            }

        } catch (Exception $e) {
            error_log('Order Creation Error: ' . $e->getMessage());
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('cart');
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

    public function success($id = null)
    {
        try {
            if (!$id) {
                throw new Exception('Không tìm thấy đơn hàng');
            }

            $userId = $this->auth->getUserId();
            $order = $this->orderModel->getOrderDetails($id);

            if (!$order || $order['userId'] !== $userId) {
                throw new Exception('Không tìm thấy đơn hàng');
            }

            $this->view('order/success', [
                'title' => 'Đặt hàng thành công',
                'order' => $order
            ]);

        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('order/history');
        }
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