<?php

namespace App\Controllers;

use App\Models\Order;
use App\Models\Cart;
use App\Models\Address;
use App\Models\ProductVariant;
use Core\Database;
use Exception;
use App\Helpers\OrderHelper;

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
                $_SESSION['no_address'] = true;
                $this->redirect('cart');
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
                    'paymentMethod' => $paymentMethod
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
                error_log('Transaction committed successfully. Order ID: ' . $orderId);

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
            //$this->redirect('cart');
        }
    }

    public function payment($id)
    {
        try {
            // Kiểm tra user đã đăng nhập
            $userId = $this->auth->getUserId();
            if (!$userId) {
                throw new Exception('Vui lòng đăng nhập để tiếp tục');
            }

            // Lấy thông tin đơn hàng
            $order = $this->orderModel->getOrderDetails($id);

            // Kiểm tra đơn hàng tồn tại và thuộc về user hiện tại
            if (!$order || $order['userId'] != $userId) {
                throw new Exception('Không tìm thấy đơn hàng');
            }

            // Kiểm tra phương thức thanh toán
            if ($order['paymentMethod'] !== 'QR_TRANSFER') {
                throw new Exception('Phương thức thanh toán không hợp lệ');
            }

            // Tạo QR code nếu chưa có
            if (empty($order['qrImage'])) {
                // Tạo QR code và lưu vào database
                $qrImage = $this->generateQRCode($order);
                $this->orderModel->updateQRImage($id, $qrImage);
                $order['qrImage'] = $qrImage;
            }

            $this->view('order/payment', [
                'title' => 'Thanh toán đơn hàng #' . $id,
                'order' => $order
            ]);

        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->redirect("order/detail/{$id}");
        }
    }

    private function generateQRCode($order)
    {
        // Thông tin tài khoản ngân hàng (có thể lấy từ config)
        $bankInfo = [
            'accountName' => 'NGUYEN VAN A',
            'accountNumber' => '123456789',
            'bankName' => 'VIETCOMBANK',
            'amount' => $order['totalAmount'],
            'description' => 'Thanh toan don hang #' . $order['id']
        ];

        // Tạo nội dung QR (có thể sử dụng thư viện để tạo QR thực tế)
        $qrContent = json_encode($bankInfo);

        // Đường dẫn đến ảnh QR mẫu (tạm thời)
        return '/ecommerce-php/public/assets/images/qr_banking.png';
    }

    public function confirmPayment($id)
    {
        try {
            $userId = $this->auth->getUserId();
            if (!$userId) {
                throw new Exception('Vui lòng đăng nhập để tiếp tục');
            }

            $order = $this->orderModel->getOrderDetails($id);
            if (!$order || $order['userId'] != $userId) {
                throw new Exception('Không tìm thấy đơn hàng');
            }

        
            // Cập nhật trạng thái thanh toán sang chờ xác nhận
            $this->orderModel->updatePaymentStatusByUser($id, 'PROCESSING', $userId);

            $_SESSION['success'] = 'Xác nhận thanh toán thành công. Chúng tôi sẽ kiểm tra và cập nhật trạng thái đơn hàng của bạn.';
            $this->redirect("order/detail/{$id}");

        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->redirect("order/payment/{$id}");
        }
    }

    public function success($id)
    {
        try {
            error_log('=== START ORDER SUCCESS PAGE ===');
            error_log('Order ID: ' . $id);

            $userId = $this->auth->getUserId();
            if (!$userId) {
                error_log('User not logged in');
                $this->redirect('login');
                return;
            }

            // Lấy thông tin đơn hàng
            $order = $this->orderModel->getOrderDetails($id);
            error_log('Order details: ' . print_r($order, true));

            if (!$order) {
                error_log('Order not found');
                throw new Exception('Không tìm thấy đơn hàng');
            }

            if ($order['userId'] != $userId) {
                error_log('Order does not belong to user');
                throw new Exception('Không có quyền xem đơn hàng này');
            }

            // Kiểm tra view có tồn tại
            $viewPath = ROOT_PATH . '/src/App/Views/order/success.php';
            if (!file_exists($viewPath)) {
                error_log('View file not found: ' . $viewPath);
                throw new Exception('Không tìm thấy template');
            }

            error_log('Rendering view with data');
            $this->view('order/success', [
                'title' => 'Đặt hàng thành công',
                'order' => $order,
                'pageTitle' => 'Đặt hàng thành công'
            ]);

        } catch (Exception $e) {
            error_log('Error in success page: ' . $e->getMessage());
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('order/history');
        }
    }

    public function history()
    {
        try {
            // Kiểm tra user đã đăng nhập
            $userId = $this->auth->getUserId();
            if (!$userId) {
                $this->redirect('login');
                return;
            }

            // Lấy tham số page từ URL, mặc định là 1
            $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
            $limit = 10; // Số đơn hàng mỗi trang
            $offset = ($page - 1) * $limit;

            // Lấy danh sách đơn hàng
            $orders = $this->orderModel->getOrdersByUserPaginated($userId, $limit, $offset);
            $totalOrders = $this->orderModel->getTotalOrdersByUser($userId);
            $totalPages = ceil($totalOrders / $limit);

            // Thêm thông tin trạng thái và màu sắc cho mỗi đơn hàng
            foreach ($orders as &$order) {
                $order['statusText'] = OrderHelper::getOrderStatusText($order['orderStatus']);
                $order['statusColor'] = OrderHelper::getOrderStatusClass($order['orderStatus']);
                $order['paymentStatusText'] = OrderHelper::getPaymentStatusText($order['paymentStatus']);
                $order['paymentStatusColor'] = OrderHelper::getPaymentStatusClass($order['paymentStatus']);
            }

            $this->view('order/history', [
                'title' => 'Lịch sử đơn hàng',
                'orders' => $orders,
                'currentPage' => $page,
                'totalPages' => $totalPages
            ]);

        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('home');
        }
    }

    public function detail($id)
    {
        try {
            $userId = $this->auth->getUserId();
            if (!$userId) {
                $this->redirect('login');
                return;
            }

            // Lấy thông tin đơn hàng
            $order = $this->orderModel->getOrderDetails($id);

            // Kiểm tra đơn hàng tồn tại và thuộc về user hiện tại
            if (!$order || $order['userId'] != $userId) {
                $_SESSION['error'] = 'Không tìm thấy đơn hàng';
                $this->redirect('order/history');
                return;
            }

            // Thêm các trạng thái và màu sắc
            $order['statusText'] = OrderHelper::getOrderStatusText($order['orderStatus']);
            $order['statusColor'] = OrderHelper::getOrderStatusClass($order['orderStatus']);
            $order['paymentStatusText'] = OrderHelper::getPaymentStatusText($order['paymentStatus']);
            $order['paymentStatusColor'] = OrderHelper::getPaymentStatusClass($order['paymentStatus']);

            $this->view('order/detail', [
                'title' => 'Chi tiết đơn hàng #' . $id,
                'order' => $order
            ]);

        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('order/history');
        }
    }

    public function cancel($id)
    {
        try {
            $userId = $this->auth->getUserId();
            if (!$userId) {
                $this->redirect('login');
                return;
            }

            $order = $this->orderModel->getOrderDetails($id);
            if (!$order || $order['userId'] != $userId) {
                throw new Exception('Không tìm thấy đơn hàng hoặc không có quyền hủy');
            }

            if (!in_array($order['orderStatus'], ['PENDING', 'PROCESSING'])) {
                throw new Exception('Không thể hủy đơn hàng ở trạng thái này');
            }

            $this->orderModel->updateOrderStatus(
                $id,
                'CANCELLED',
                'Đơn hàng đã bị hủy bởi khách hàng',
                $userId
            );

            $_SESSION['success'] = 'Hủy đơn hàng thành công';
            $this->redirect("order/detail/{$id}");

        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->redirect("order/detail/{$id}");
        }
    }

    public function confirmDelivery($id)
    {
        try {
            $this->db->beginTransaction();

            $userId = $this->auth->getUserId();
            if (!$userId) {
                error_log("User not logged in");
                $this->redirect('login');
                return;
            }

            $orderId = $id;
            $status = 'DELIVERED';
            $note = 'Đã nhận hàng và thanh toán';
            $userId = $this->auth->getUserId();

            // Cập nhật trạng thái đơn hàng
            $this->orderModel->updateOrderStatus($orderId, $status, $note, $userId);

            // Lấy thông tin đơn hàng
            $order = $this->orderModel->getOrderById($orderId);

            // Nếu là thanh toán COD và đã giao hàng thành công
            if ($order['paymentMethod'] === 'CASH_ON_DELIVERY') {
                // Cập nhật trạng thái thanh toán
                $this->orderModel->updatePaymentStatus($orderId, 'PAID', 'Thanh toán khi nhận hàng');
            }

            $this->db->commit();
            $_SESSION['success'] = 'Xác nhận giao hàng thành công';
            $this->redirect("order/detail/{$id}");

        } catch (Exception $e) {
            $this->db->rollBack();
            $_SESSION['error'] = $e->getMessage();
            $this->redirect("order/detail/{$id}");
        }
    }

    // Cập nhật yêu cầu hoàn trả

    public function returnRequest($id)
    {
        try {
            $userId = $this->auth->getUserId();
            if (!$userId) {
                $this->redirect('login');
                return;
            }

            $order = $this->orderModel->getOrderDetails($id);
            $status = 'RETURN_REQUEST';
            $note = $_POST['returnReason'] ?? 'Yêu cầu hoàn trả đơn hàng bởi khách hàng';

            if (!$order || $order['userId'] != $userId) {
                throw new Exception('Không tìm thấy đơn hàng hoặc không có quyền hoàn trả');
            }

            if ($order['orderStatus'] !== 'SHIPPING') {
                throw new Exception('Không thể hoàn trả đơn hàng ở trạng thái này');
            }

            $this->orderModel->updateOrderStatus(
                $id,
                $status,
                $note,
                $userId
            );

            // Cập nhật trạng thái đơn hàng
            $this->orderModel->update($id, ['orderStatus' => $status]);

            $_SESSION['success'] = 'Xác nhận yêu cầu hoàn trả đơn hàng';
            $this->redirect("order/detail/{$id}");

        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->redirect("order/detail/{$id}");
        }
    }
}