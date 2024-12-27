<?php

namespace App\Models;

use PDO;
use PDOException;
use Exception;

class Order extends BaseModel
{
    protected $table = 'orders';

    // Tạo đơn hàng
    public function create($data)
    {
        try {
            $sql = "INSERT INTO orders (userId, addressId, totalAmount, paymentMethod, orderStatus, paymentStatus) 
                    VALUES (:userId, :addressId, :totalAmount, :paymentMethod, :orderStatus, :paymentStatus)";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($data);
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            throw new Exception("Lỗi khi tạo đơn hàng: " . $e->getMessage());
        }
    }

    // Tạo sản phẩm trong đơn hàng
    public function createOrderItem($data)
    {
        try {
            $sql = "INSERT INTO order_items (orderId, variantId, quantity, price) 
                    VALUES (:orderId, :variantId, :quantity, :price)";
            
            $stmt = $this->db->prepare($sql);
            return $stmt->execute($data);
        } catch (PDOException $e) {
            throw new Exception("Lỗi khi tạo chi tiết đơn hàng: " . $e->getMessage());
        }
    }

    // Tạo thanh toán
    public function createPayment($data)
    {
        try {
            $sql = "INSERT INTO payments (orderId, amount, paymentMethod, paymentStatus) 
                    VALUES (:orderId, :amount, :paymentMethod, :paymentStatus)";
            
            $stmt = $this->db->prepare($sql);
            return $stmt->execute($data);
        } catch (PDOException $e) {
            throw new Exception("Lỗi khi tạo thanh toán: " . $e->getMessage());
        }
    }

    // Kiểm tra xem trạng thái có thể được cập nhật không
    public function canUpdateStatus($currentStatus, $newStatus) 
    {
        $validTransitions = [
            'PENDING' => ['PROCESSING', 'CANCELLED'],
            'PROCESSING' => ['CONFIRMED', 'CANCELLED'],
            'CONFIRMED' => ['SHIPPING', 'CANCELLED'],
            'SHIPPING' => ['DELIVERED', 'RETURN_REQUEST'],
            'DELIVERED' => [],
            'RETURN_REQUEST' => ['RETURN_APPROVED', 'DELIVERED'],
            'RETURN_APPROVED' => ['RETURNED'],
            'RETURNED' => [],
            'CANCELLED' => []
        ];

        return in_array($newStatus, $validTransitions[$currentStatus] ?? []);
    }

    // Cập nhật trạng thái đơn hàng
    public function updateOrderStatus($orderId, $status, $note, $updatedBy)
    {
        try {
            // Cập nhật trạng thái đơn hàng
            $sql = "UPDATE orders SET orderStatus = :status WHERE id = :orderId";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['status' => $status, 'orderId' => $orderId]);

            // Thêm vào lịch sử
            $this->createOrderHistory([
                'orderId' => $orderId,
                'status' => $status,
                'note' => $note,
                'createdBy' => $updatedBy
            ]);

        } catch (PDOException $e) {
            throw new Exception("Error: " . $e->getMessage());
        }
    }

    // Lấy chi tiết đơn hàng
    public function getOrderDetails($orderId)
    {
        try {
            // 1. Lấy thông tin cơ bản của đơn hàng
            $sql = "SELECT o.*, 
                    a.fullName, a.phoneNumber as phone, a.address,
                    u.fullName as userName, u.email as userEmail,
                    p.paymentMethod as paymentMethod, 
                    p.paymentStatus as paymentStatus,
                    p.qrImage as bankingImage
                    FROM orders o
                    LEFT JOIN addresses a ON o.addressId = a.id
                    LEFT JOIN users u ON o.userId = u.id
                    LEFT JOIN payments p ON o.id = p.orderId
                    WHERE o.id = :orderId";

            $stmt = $this->db->prepare($sql);
            $stmt->execute(['orderId' => $orderId]);
            $order = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($order) {
                // 2. Lấy chi tiết sản phẩm trong đơn hàng
                $sql = "SELECT oi.*, 
                        p.productName, p.category,
                        pv.sku, pv.price as originalPrice,
                        pi.imageUrl,
                        GROUP_CONCAT(DISTINCT CONCAT(vt.name, ': ', vv.value) SEPARATOR ', ') as variantInfo
                        FROM order_items oi
                        JOIN product_variants pv ON oi.variantId = pv.id
                        JOIN products p ON pv.productId = p.id
                        LEFT JOIN product_images pi ON p.id = pi.productId AND pi.isThumbnail = 1
                        LEFT JOIN variant_combinations vc ON pv.id = vc.productVariantId
                        LEFT JOIN variant_values vv ON vc.variantValueId = vv.id
                        LEFT JOIN variant_types vt ON vv.variantTypeId = vt.id
                        WHERE oi.orderId = :orderId
                        GROUP BY oi.id, p.productName, p.category, pv.sku, pv.price, pi.imageUrl";

                $stmt = $this->db->prepare($sql);
                $stmt->execute(['orderId' => $orderId]);
                $order['items'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // 3. Lấy lịch sử đơn hàng với thêm statusText
                $sql = "SELECT oh.*, 
                        CASE 
                            WHEN oh.createdBy = o.userId THEN 'Khách hàng'
                            ELSE 'Hệ thống'
                        END as updatedByText,
                        CASE 
                            WHEN oh.status = 'PENDING' THEN 'Chờ xác nhận'
                            WHEN oh.status = 'PROCESSING' THEN 'Đang xử lý'
                            WHEN oh.status = 'SHIPPING' THEN 'Đang giao hàng'
                            WHEN oh.status = 'DELIVERED' THEN 'Đã giao hàng'
                            WHEN oh.status = 'CANCELLED' THEN 'Đã hủy'
                            ELSE oh.status
                        END as statusText
                        FROM order_history oh
                        JOIN orders o ON oh.orderId = o.id
                        WHERE oh.orderId = :orderId
                        ORDER BY oh.createdAt DESC";

                $stmt = $this->db->prepare($sql);
                $stmt->execute(['orderId' => $orderId]);
                $order['history'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // Thêm status từ orderStatus
                $order['status'] = $order['orderStatus'];
            }

            return $order;
        } catch (PDOException $e) {
            error_log("Error getting order details: " . $e->getMessage());
            throw new Exception("Lỗi khi lấy chi tiết đơn hàng: " . $e->getMessage());
        }
    }

    // Lấy danh sách đơn hàng của người dùng
    public function getOrdersByUser($userId)
    {
        try {
            $sql = "SELECT o.*, 
                    p.method as paymentMethod,
                    p.status as paymentStatus
                    FROM orders o
                    LEFT JOIN payments p ON o.id = p.orderId
                    WHERE o.userId = :userId
                    ORDER BY o.createdAt DESC";

            $stmt = $this->db->prepare($sql);
            $stmt->execute(['userId' => $userId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Lỗi khi lấy lịch sử đơn hàng: " . $e->getMessage());
        }
    }

    // Tạo lịch sử đơn hàng
    public function createOrderHistory($data)
    {
        try {
            $sql = "INSERT INTO order_history (orderId, status, note, createdBy) 
                    VALUES (:orderId, :status, :note, :createdBy)";
            
            $stmt = $this->db->prepare($sql);
            return $stmt->execute($data);
        } catch (PDOException $e) {
            throw new Exception("Lỗi khi tạo lịch sử đơn hàng: " . $e->getMessage());
        }
    }

    // Lấy lịch sử đơn hàng
    public function getOrderHistory($orderId)
    {
        try {
            $sql = "SELECT oh.*, 
                    CASE 
                        WHEN oh.updatedBy = o.userId THEN 'Khách hàng'
                        ELSE 'Hệ thống'
                    END as updatedByText
                    FROM order_history oh
                    JOIN orders o ON oh.orderId = o.id
                    WHERE oh.orderId = :orderId
                    ORDER BY oh.createdAt DESC";

            $stmt = $this->db->prepare($sql);
            $stmt->execute(['orderId' => $orderId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Lỗi khi lấy lịch sử đơn hàng: " . $e->getMessage());
        }
    }

    // Cập nhật trạng thái thanh toán
    public function updatePaymentStatus($orderId, $status, $note = '')
    {
        try {
            // Cập nhật trạng thái thanh toán trong bảng payments
            $sql = "UPDATE payments 
                    SET paymentStatus = :status,
                        note = :note,
                        updatedAt = CURRENT_TIMESTAMP
                    WHERE orderId = :orderId";
                    
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                'status' => $status,
                'note' => $note,
                'orderId' => $orderId
            ]);
        } catch (PDOException $e) {
            throw new Exception("Lỗi khi cập nhật trạng thái thanh toán: " . $e->getMessage());
        }
    }

    // Cập nhật QR image
    public function updateQRImage($orderId, $qrImage)
    {
        try {
            $sql = "UPDATE payments SET qrImage = :qrImage 
                    WHERE orderId = :orderId";
            
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                'orderId' => $orderId,
                'qrImage' => $qrImage
            ]);
        } catch (PDOException $e) {
            throw new Exception("Lỗi khi cập nhật QR image: " . $e->getMessage());
        }
    }

    // Lấy danh sách đơn hàng của người dùng theo phân trang
    public function getOrdersByUserPaginated($userId, $limit, $offset)
    {
        try {
            $sql = "SELECT o.*, 
                    p.paymentMethod, p.paymentStatus,
                    (SELECT COUNT(*) FROM order_items WHERE orderId = o.id) as totalItems
                    FROM orders o
                    LEFT JOIN payments p ON o.id = p.orderId
                    WHERE o.userId = :userId
                    ORDER BY o.createdAt DESC
                    LIMIT :limit OFFSET :offset";

            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Lỗi khi lấy danh sách đơn hàng: " . $e->getMessage());
        }
    }

    public function getTotalOrdersByUser($userId)
    {
        try {
            $sql = "SELECT COUNT(*) FROM orders WHERE userId = :userId";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['userId' => $userId]);
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            throw new Exception("Lỗi khi đếm tổng số đơn hàng: " . $e->getMessage());
        }
    }

    // Lấy tất cả đơn hàng
    public function getAllOrders()
    {
        try {
            $sql = "SELECT o.*,
                    u.fullName as customerName,
                    u.phone,
                    a.fullName as receiverName,
                    a.phoneNumber as receiverPhone,
                    a.address,
                    p.paymentMethod,
                    p.paymentStatus,
                    p.amount as paidAmount,
                    GROUP_CONCAT(DISTINCT oi.id) as orderItemIds,
                    GROUP_CONCAT(DISTINCT oh.status) as statusHistory,
                    GROUP_CONCAT(DISTINCT oh.note) as statusNotes,
                    GROUP_CONCAT(DISTINCT oh.createdAt) as statusDates,
                    GROUP_CONCAT(DISTINCT 
                        CONCAT(pv.id, ':', oi.quantity, ':', oi.price, ':', prod.productName)
                        ORDER BY oi.id
                    ) as items
                    FROM orders o
                    LEFT JOIN users u ON o.userId = u.id
                    LEFT JOIN addresses a ON o.addressId = a.id
                    LEFT JOIN payments p ON o.id = p.orderId
                    LEFT JOIN order_items oi ON o.id = oi.orderId
                    LEFT JOIN product_variants pv ON oi.variantId = pv.id
                    LEFT JOIN products prod ON pv.productId = prod.id
                    LEFT JOIN order_history oh ON o.id = oh.orderId
                    GROUP BY o.id
                    ORDER BY o.createdAt DESC";

            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Xử lý dữ liệu sau khi lấy
            foreach ($orders as &$order) {
                // Chuyển đổi chuỗi items thành mảng
                $itemsArray = [];
                if (!empty($order['items'])) {
                    $items = explode(',', $order['items']);
                    foreach ($items as $item) {
                        list($variantId, $quantity, $price, $productName) = explode(':', $item);
                        $itemsArray[] = [
                            'variantId' => $variantId,
                            'quantity' => $quantity,
                            'price' => $price,
                            'productName' => $productName
                        ];
                    }
                }
                $order['items'] = $itemsArray;

                // Chuyển đổi lịch sử trạng thái
                $statusHistory = [];
                if (!empty($order['statusHistory'])) {
                    $statuses = explode(',', $order['statusHistory']);
                    $notes = explode(',', $order['statusNotes']);
                    $dates = explode(',', $order['statusDates']);
                    
                    for ($i = 0; $i < count($statuses); $i++) {
                        $statusHistory[] = [
                            'status' => $statuses[$i],
                            'note' => $notes[$i] ?? '',
                            'date' => $dates[$i] ?? ''
                        ];
                    }
                }
                $order['statusHistory'] = $statusHistory;

                // Xóa các trường không cần thiết
                unset($order['statusNotes']);
                unset($order['statusDates']);
            }

            return $orders;
        } catch (PDOException $e) {
            throw new Exception("Lỗi khi lấy danh sách đơn hàng: " . $e->getMessage());
        }
    }

    // Lấy danh sách đơn hàng theo trạng thái
    public function getOrdersByStatus($status)
    {
        try {
            $sql = "SELECT o.*, 
                    u.fullName,
                    u.phone,
                    a.fullName as receiverName,
                    a.phoneNumber as receiverPhone,
                    a.address,
                    p.paymentMethod,
                    p.paymentStatus,
                    p.amount as paidAmount
                    FROM orders o
                    LEFT JOIN users u ON o.userId = u.id
                    LEFT JOIN addresses a ON o.addressId = a.id 
                    LEFT JOIN payments p ON o.id = p.orderId
                    WHERE o.orderStatus = :status
                    ORDER BY o.createdAt DESC";

            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':status', $status, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Lỗi khi lấy danh sách đơn hàng theo trạng thái: " . $e->getMessage());
        }
    }

    // Lấy đơn hàng chờ xử lý
    public function getPendingOrders()
    {
        return $this->getOrdersByStatus('PENDING');
    }

    // Lấy đơn hàng theo ID
    public function getOrderById($id)
    {
        try {
            $sql = "SELECT o.*,
                    u.fullName as customerName,
                    u.phone,
                    a.fullName as receiverName,
                    a.phoneNumber as receiverPhone,
                    a.address,
                    p.paymentMethod,
                    p.paymentStatus,
                    p.amount as paidAmount,
                    GROUP_CONCAT(DISTINCT oi.id) as orderItemIds,
                    GROUP_CONCAT(DISTINCT oh.status) as statusHistory,
                    GROUP_CONCAT(DISTINCT oh.note) as statusNotes,
                    GROUP_CONCAT(DISTINCT oh.createdAt) as statusDates,
                    GROUP_CONCAT(DISTINCT 
                        CONCAT(pv.id, ':', oi.quantity, ':', oi.price, ':', prod.productName, ':', IFNULL(pv.sku, 'N/A'))
                        ORDER BY oi.id
                    ) as items
                    FROM orders o
                    LEFT JOIN users u ON o.userId = u.id
                    LEFT JOIN addresses a ON o.addressId = a.id
                    LEFT JOIN payments p ON o.id = p.orderId
                    LEFT JOIN order_items oi ON o.id = oi.orderId
                    LEFT JOIN product_variants pv ON oi.variantId = pv.id
                    LEFT JOIN products prod ON pv.productId = prod.id
                    LEFT JOIN order_history oh ON o.id = oh.orderId
                    WHERE o.id = ?
                    GROUP BY o.id";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);
            $order = $stmt->fetch();

            // Xử lý dữ liệu trả về
            if ($order) {
                // Xử lý items
                $itemsArray = [];
                if (!empty($order['items'])) {
                    $items = explode(',', $order['items']);
                    foreach ($items as $item) {
                        list($variantId, $quantity, $price, $productName, $sku) = explode(':', $item);
                        $itemsArray[] = [
                            'variantId' => $variantId,
                            'quantity' => $quantity,
                            'price' => $price,
                            'productName' => $productName,
                            'sku' => $sku
                        ];
                    }
                }
                $order['items'] = $itemsArray;

                // Xử lý lịch sử trạng thái
                $statusHistory = [];
                if (!empty($order['statusHistory'])) {
                    $statuses = explode(',', $order['statusHistory']);
                    $notes = explode(',', $order['statusNotes']);
                    $dates = explode(',', $order['statusDates']);
                    
                    for ($i = 0; $i < count($statuses); $i++) {
                        $statusHistory[] = [
                            'status' => $statuses[$i],
                            'note' => $notes[$i] ?? '',
                            'date' => $dates[$i] ?? ''
                        ];
                    }
                }
                $order['statusHistory'] = $statusHistory;
            }

            return $order;
        } catch (PDOException $e) {
            throw new Exception("Lỗi khi lấy thông tin đơn hàng: " . $e->getMessage());
        }
    }

    // Yêu cầu trả hàng
    public function requestReturn($orderId, $reason)
    {
        try {
            $this->db->beginTransaction();
            
            // Cập nhật trạng thái đơn hàng
            $sql = "UPDATE orders SET 
                    orderStatus = 'RETURN_REQUEST',
                    returnReason = :reason,
                    returnRequestDate = CURRENT_TIMESTAMP
                    WHERE id = :orderId";
                    
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'orderId' => $orderId,
                'reason' => $reason
            ]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    // Lấy tổng số đơn hàng
    public function getTotalOrders()
    {
        try {
            $sql = "SELECT COUNT(*) FROM {$this->table}";
            return $this->db->query($sql)->fetchColumn();
        } catch (PDOException $e) {
            error_log("Error getting total orders: " . $e->getMessage());
            return 0;
        }
    }

    // Lấy tổng doanh thu tháng hiện tại
    public function getCurrentMonthRevenue()
    {
        try {
            $sql = "SELECT COALESCE(SUM(totalAmount), 0) 
                    FROM {$this->table} 
                    WHERE MONTH(createdAt) = MONTH(CURRENT_DATE)
                    AND YEAR(createdAt) = YEAR(CURRENT_DATE)
                    AND orderStatus = 'DELIVERED'";
            return $this->db->query($sql)->fetchColumn();
        } catch (PDOException $e) {
            error_log("Error getting current month revenue: " . $e->getMessage());
            return 0;
        }
    }

    // Lấy doanh thu theo tháng
    public function getMonthlyRevenue()
    {
        try {
            $sql = "SELECT 
                        DATE_FORMAT(createdAt, '%m/%Y') as label,
                        COALESCE(SUM(totalAmount), 0) as value
                    FROM {$this->table}
                    WHERE orderStatus = 'DELIVERED'
                    GROUP BY DATE_FORMAT(createdAt, '%Y-%m')
                    ORDER BY createdAt DESC
                    LIMIT 12";
            
            $result = $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
            
            return [
                'labels' => array_column($result, 'label'),
                'values' => array_column($result, 'value')
            ];
        } catch (PDOException $e) {
            error_log("Error getting monthly revenue: " . $e->getMessage());
            return ['labels' => [], 'values' => []];
        }
    }

    // Lấy phân bố trạng thái đơn hàng
    public function getOrderStatusDistribution()
    {
        try {
            $sql = "SELECT 
                        orderStatus as label,
                        COUNT(*) as value
                    FROM {$this->table}
                    GROUP BY orderStatus";
            
            $result = $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
            
            return [
                'labels' => array_map([$this, 'translateOrderStatus'], array_column($result, 'label')),
                'values' => array_column($result, 'value')
            ];
        } catch (PDOException $e) {
            error_log("Error getting order status distribution: " . $e->getMessage());
            return ['labels' => [], 'values' => []];
        }
    }

    // Dịch trạng thái đơn hàng
    private function translateOrderStatus($status)
    {
        $translations = [
            'PENDING' => 'Chờ xử lý',
            'PROCESSING' => 'Đang xử lý',
            'CONFIRMED' => 'Đã xác nhận',
            'SHIPPING' => 'Đang giao hàng',
            'DELIVERED' => 'Đã giao hàng',
            'CANCELLED' => 'Đã hủy',
            'RETURN_REQUEST' => 'Yêu cầu trả hàng',
            'RETURNED' => 'Đã trả hàng'
        ];
        
        return $translations[$status] ?? $status;
    }
    
    // Lấy thông tin trạng thái đơn hàng
    private function getOrderStatusInfo($status)
    {
        $statusInfo = [
            'PENDING' => ['color' => 'warning', 'text' => 'Chờ xử lý'],
            'PROCESSING' => ['color' => 'info', 'text' => 'Đang xử lý'],
            'CONFIRMED' => ['color' => 'primary', 'text' => 'Đã xác nhận'],
            'SHIPPING' => ['color' => 'info', 'text' => 'Đang giao hàng'],
            'DELIVERED' => ['color' => 'success', 'text' => 'Đã giao hàng'],
            'CANCELLED' => ['color' => 'danger', 'text' => 'Đã hủy'],
            'RETURN_REQUEST' => ['color' => 'warning', 'text' => 'Yêu cầu trả hàng'],
            'RETURNED' => ['color' => 'secondary', 'text' => 'Đã trả hàng']
        ];
        
        return $statusInfo[$status] ?? ['color' => 'secondary', 'text' => $status];
    }
}
