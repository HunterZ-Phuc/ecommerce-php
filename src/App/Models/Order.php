<?php

namespace App\Models;

use PDO;
use Exception;

class Order extends BaseModel
{
    protected $table = 'orders';

    public function createOrder($data)
    {
        try {
            $this->db->beginTransaction();

            // 1. Tạo đơn hàng
            $sql = "INSERT INTO orders (userId, addressId, totalAmount, paymentMethod, note) 
                    VALUES (:userId, :addressId, :totalAmount, :paymentMethod, :note)";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'userId' => $data['userId'],
                'addressId' => $data['addressId'],
                'totalAmount' => $data['totalAmount'],
                'paymentMethod' => $data['paymentMethod'],
                'note' => $data['note'] ?? null
            ]);
            
            $orderId = $this->db->lastInsertId();

            // 2. Thêm chi tiết đơn hàng
            $sql = "INSERT INTO order_items (orderId, variantId, quantity, price) 
                    VALUES (:orderId, :variantId, :quantity, :price)";
            $stmt = $this->db->prepare($sql);

            foreach ($data['items'] as $item) {
                $stmt->execute([
                    'orderId' => $orderId,
                    'variantId' => $item['variantId'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price']
                ]);

                // Cập nhật số lượng sản phẩm
                $sql = "UPDATE product_variants 
                       SET quantity = quantity - :quantity 
                       WHERE id = :variantId";
                $stmt2 = $this->db->prepare($sql);
                $stmt2->execute([
                    'quantity' => $item['quantity'],
                    'variantId' => $item['variantId']
                ]);
            }

            // 3. Tạo lịch sử đơn hàng
            $sql = "INSERT INTO order_history (orderId, status, note, createdBy) 
                    VALUES (:orderId, 'PENDING', 'Đơn hàng mới', :userId)";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'orderId' => $orderId,
                'userId' => $data['userId']
            ]);

            // 4. Tạo thanh toán
            $sql = "INSERT INTO payments (orderId, amount, method) 
                    VALUES (:orderId, :amount, :method)";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'orderId' => $orderId,
                'amount' => $data['totalAmount'],
                'method' => $data['paymentMethod']
            ]);

            $this->db->commit();
            return $orderId;

        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function getOrdersByUser($userId)
    {
        $sql = "SELECT o.*, 
                       p.method as paymentMethod,
                       p.status as paymentStatus,
                       a.fullName, a.phone, a.address
                FROM orders o
                LEFT JOIN payments p ON o.id = p.orderId
                LEFT JOIN addresses a ON o.addressId = a.id
                WHERE o.userId = :userId
                ORDER BY o.createdAt DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['userId' => $userId]);
        return $stmt->fetchAll();
    }

    public function getOrderDetails($orderId)
    {
        // Lấy thông tin đơn hàng
        $sql = "SELECT o.*, 
                       p.method as paymentMethod,
                       p.status as paymentStatus,
                       p.bankingImage,
                       a.fullName, a.phone, a.address,
                       u.username
                FROM orders o
                LEFT JOIN payments p ON o.id = p.orderId
                LEFT JOIN addresses a ON o.addressId = a.id
                LEFT JOIN users u ON o.userId = u.id
                WHERE o.id = :orderId";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['orderId' => $orderId]);
        $order = $stmt->fetch();

        if (!$order) {
            return null;
        }

        // Lấy chi tiết sản phẩm
        $sql = "SELECT oi.*, 
                       p.productName,
                       pv.sku,
                       GROUP_CONCAT(CONCAT(a.name, ': ', av.value)) as variantName,
                       pi.imageUrl
                FROM order_items oi
                JOIN product_variants pv ON oi.variantId = pv.id
                JOIN products p ON pv.productId = p.id
                JOIN variant_combinations vc ON pv.id = vc.variantId
                JOIN attribute_values av ON vc.attributeValueId = av.id
                JOIN attributes a ON av.attributeId = a.id
                LEFT JOIN product_images pi ON pv.id = pi.variantId AND pi.isDefault = 1
                WHERE oi.orderId = :orderId
                GROUP BY oi.id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['orderId' => $orderId]);
        $order['items'] = $stmt->fetchAll();

        // Lấy lịch sử đơn hàng
        $sql = "SELECT oh.*, 
                       u.username as createdByUser
                FROM order_history oh
                JOIN users u ON oh.createdBy = u.id
                WHERE oh.orderId = :orderId
                ORDER BY oh.createdAt DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['orderId' => $orderId]);
        $order['history'] = $stmt->fetchAll();

        return $order;
    }

    public function updateOrderStatus($orderId, $status, $note, $userId)
    {
        try {
            $this->db->beginTransaction();

            // Cập nhật trạng thái đơn hàng
            $sql = "UPDATE orders SET orderStatus = :status WHERE id = :orderId";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'status' => $status,
                'orderId' => $orderId
            ]);

            // Thêm vào lịch sử
            $sql = "INSERT INTO order_history (orderId, status, note, createdBy) 
                    VALUES (:orderId, :status, :note, :userId)";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'orderId' => $orderId,
                'status' => $status,
                'note' => $note,
                'userId' => $userId
            ]);

            $this->db->commit();
            return true;

        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function updatePaymentStatus($orderId, $status, $transactionCode = null)
    {
        try {
            $this->db->beginTransaction();

            $sql = "UPDATE payments 
                   SET status = :status, transactionCode = :transactionCode 
                   WHERE orderId = :orderId";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'status' => $status,
                'transactionCode' => $transactionCode,
                'orderId' => $orderId
            ]);

            if ($status === 'PAID') {
                $sql = "UPDATE orders SET paymentStatus = 'PAID' WHERE id = :orderId";
                $stmt = $this->db->prepare($sql);
                $stmt->execute(['orderId' => $orderId]);
            }

            $this->db->commit();
            return true;

        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function updateBankingImage($orderId, $imagePath)
    {
        $sql = "UPDATE payments 
                SET bankingImage = :imagePath 
                WHERE orderId = :orderId";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'imagePath' => $imagePath,
            'orderId' => $orderId
        ]);
    }

    public function getPendingOrders()
    {
        $sql = "SELECT o.*, 
                       p.method as paymentMethod,
                       p.status as paymentStatus,
                       a.fullName, a.phone, a.address,
                       u.username
                FROM orders o
                LEFT JOIN payments p ON o.id = p.orderId
                LEFT JOIN addresses a ON o.addressId = a.id
                LEFT JOIN users u ON o.userId = u.id
                WHERE o.orderStatus IN ('PENDING', 'PROCESSING')
                ORDER BY o.createdAt DESC";

        return $this->db->query($sql)->fetchAll();
    }

    public function getOrdersByStatus($status)
    {
        $sql = "SELECT o.*, 
                       p.method as paymentMethod,
                       p.status as paymentStatus,
                       a.fullName, a.phone, a.address,
                       u.username
                FROM orders o
                LEFT JOIN payments p ON o.id = p.orderId
                LEFT JOIN addresses a ON o.addressId = a.id
                LEFT JOIN users u ON o.userId = u.id
                WHERE o.orderStatus = :status
                ORDER BY o.createdAt DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['status' => $status]);
        return $stmt->fetchAll();
    }

    public function countOrdersByStatus($status)
    {
        $sql = "SELECT COUNT(*) as count FROM orders WHERE orderStatus = :status";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['status' => $status]);
        return $stmt->fetch()['count'];
    }

    public function getRevenueStats($startDate = null, $endDate = null)
    {
        $sql = "SELECT 
                    DATE(o.createdAt) as date,
                    COUNT(*) as totalOrders,
                    SUM(o.totalAmount) as revenue,
                    COUNT(CASE WHEN o.orderStatus = 'DELIVERED' THEN 1 END) as completedOrders,
                    COUNT(CASE WHEN o.orderStatus = 'CANCELLED' THEN 1 END) as cancelledOrders
                FROM orders o
                WHERE o.orderStatus IN ('DELIVERED', 'CANCELLED')";

        if ($startDate) {
            $sql .= " AND DATE(o.createdAt) >= :startDate";
        }
        if ($endDate) {
            $sql .= " AND DATE(o.createdAt) <= :endDate";
        }

        $sql .= " GROUP BY DATE(o.createdAt)
                  ORDER BY date DESC";

        $stmt = $this->db->prepare($sql);
        
        if ($startDate) {
            $stmt->bindParam(':startDate', $startDate);
        }
        if ($endDate) {
            $stmt->bindParam(':endDate', $endDate);
        }

        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getTopProducts($limit = 10)
    {
        $sql = "SELECT 
                    p.id,
                    p.productName,
                    COUNT(DISTINCT o.id) as totalOrders,
                    SUM(oi.quantity) as totalQuantity,
                    SUM(oi.quantity * oi.price) as totalRevenue
                FROM products p
                JOIN product_variants pv ON p.id = pv.productId
                JOIN order_items oi ON pv.id = oi.variantId
                JOIN orders o ON oi.orderId = o.id
                WHERE o.orderStatus = 'DELIVERED'
                GROUP BY p.id
                ORDER BY totalQuantity DESC
                LIMIT :limit";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
