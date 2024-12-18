<?php

namespace App\Models;

use PDO;
use PDOException;
use Exception;

class Order extends BaseModel
{
    protected $table = 'orders';

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

    public function updateBankingImage($orderId, $imagePath)
    {
        try {
            $sql = "UPDATE payments SET bankingImage = :imagePath 
                    WHERE orderId = :orderId";
            
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                'orderId' => $orderId,
                'imagePath' => $imagePath
            ]);
        } catch (PDOException $e) {
            throw new Exception("Lỗi khi cập nhật ảnh thanh toán: " . $e->getMessage());
        }
    }

    public function updateOrderStatus($orderId, $status, $note = '', $updatedBy = null)
    {
        try {
            $this->db->beginTransaction();

            // Cập nhật trạng thái đơn hàng
            $sql = "UPDATE orders SET orderStatus = :status WHERE id = :orderId";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'orderId' => $orderId,
                'status' => $status
            ]);

            // Thêm vào lịch sử
            $this->createOrderHistory([
                'orderId' => $orderId,
                'status' => $status,
                'note' => $note,
                'updatedBy' => $updatedBy
            ]);

            $this->db->commit();
            return true;

        } catch (PDOException $e) {
            $this->db->rollBack();
            throw new Exception("Lỗi khi cập nhật trạng thái đơn hàng: " . $e->getMessage());
        }
    }

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

    public function updatePaymentStatus($orderId, $status, $note = '')
    {
        try {
            $sql = "UPDATE payments 
                    SET paymentStatus = :status, note = :note 
                    WHERE orderId = :orderId";
                    
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                'orderId' => $orderId,
                'status' => $status,
                'note' => $note
            ]);
        } catch (PDOException $e) {
            throw new Exception("Lỗi khi cập nhật trạng thái thanh toán: " . $e->getMessage());
        }
    }

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
}
