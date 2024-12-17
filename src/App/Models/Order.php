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
            // Lấy thông tin đơn hàng
            $sql = "SELECT o.*, 
                    p.method as paymentMethod, 
                    p.status as paymentStatus,
                    p.bankingImage,
                    a.fullName, a.phone, a.address,
                    u.fullName as userName,
                    u.email as userEmail
                    FROM orders o
                    LEFT JOIN payments p ON o.id = p.orderId
                    LEFT JOIN addresses a ON o.addressId = a.id 
                    LEFT JOIN users u ON o.userId = u.id
                    WHERE o.id = :orderId";

            $stmt = $this->db->prepare($sql);
            $stmt->execute(['orderId' => $orderId]);
            $order = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($order) {
                // Lấy chi tiết sản phẩm trong đơn hàng
                $sql = "SELECT oi.*, 
                        p.productName,
                        pv.variantName,
                        pi.imageUrl
                        FROM order_items oi
                        JOIN product_variants pv ON oi.variantId = pv.id
                        JOIN products p ON pv.productId = p.id
                        LEFT JOIN product_images pi ON p.id = pi.productId
                        WHERE oi.orderId = :orderId";

                $stmt = $this->db->prepare($sql);
                $stmt->execute(['orderId' => $orderId]);
                $order['items'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }

            return $order;
        } catch (PDOException $e) {
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
}
