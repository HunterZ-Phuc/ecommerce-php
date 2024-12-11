<?php
namespace App\Models;

class Cart extends BaseModel
{
    protected $table = 'carts';

    public function getCartItems($userId)
    {
        $sql = "SELECT c.*, 
                       p.id as productId,
                       p.productName,
                       p.salePercent, 
                       pv.price as originalPrice,
                       CASE 
                           WHEN p.salePercent > 0 THEN pv.price * (1 - p.salePercent/100)
                           ELSE pv.price 
                       END as finalPrice,
                       pv.quantity as stockQuantity,
                       pi.imageUrl,
                       vc.id as combinationId,
                       vt.name as typeName,
                       vv.value
                FROM {$this->table} c
                JOIN product_variants pv ON c.variantId = pv.id
                JOIN products p ON pv.productId = p.id
                LEFT JOIN product_images pi ON pv.id = pi.variantId
                LEFT JOIN variant_combinations vc ON pv.id = vc.productVariantId
                LEFT JOIN variant_values vv ON vc.variantValueId = vv.id
                LEFT JOIN variant_types vt ON vv.variantTypeId = vt.id
                WHERE c.userId = :userId";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['userId' => $userId]);
        $results = $stmt->fetchAll();

        // Tổ chức lại dữ liệu
        $cartItems = [];
        foreach ($results as $row) {
            if (!isset($cartItems[$row['variantId']])) {
                $cartItems[$row['variantId']] = [
                    'id' => $row['id'],
                    'productId' => $row['productId'],
                    'variantId' => $row['variantId'],
                    'productName' => $row['productName'],
                    'originalPrice' => $row['originalPrice'],
                    'finalPrice' => $row['finalPrice'],
                    'salePercent' => $row['salePercent'],
                    'quantity' => $row['quantity'],
                    'stockQuantity' => $row['stockQuantity'],
                    'imageUrl' => $row['imageUrl'],
                    'variantCombinations' => []
                ];
            }
            
            if ($row['typeName'] && $row['value']) {
                $cartItems[$row['variantId']]['variantCombinations'][] = [
                    'typeName' => $row['typeName'],
                    'value' => $row['value']
                ];
            }
        }

        return array_values($cartItems);
    }

    public function addToCart($userId, $variantId, $quantity)
    {
        // Kiểm tra xem sản phẩm đã có trong giỏ hàng chưa
        $sql = "SELECT * FROM {$this->table} WHERE userId = :userId AND variantId = :variantId";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'userId' => $userId,
            'variantId' => $variantId
        ]);
        $existingItem = $stmt->fetch();

        if ($existingItem) {
            // Nếu đã có, cập nhật số lượng
            $sql = "UPDATE {$this->table} 
                   SET quantity = quantity + :quantity 
                   WHERE userId = :userId AND variantId = :variantId";
        } else {
            // Nếu chưa có, thêm mới
            $sql = "INSERT INTO {$this->table} (userId, variantId, quantity) 
                   VALUES (:userId, :variantId, :quantity)";
        }

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'userId' => $userId,
            'variantId' => $variantId,
            'quantity' => $quantity
        ]);
    }

    public function updateQuantity($userId, $variantId, $quantity)
    {
        $sql = "UPDATE {$this->table} 
                SET quantity = :quantity 
                WHERE userId = :userId AND variantId = :variantId";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'userId' => $userId,
            'variantId' => $variantId,
            'quantity' => $quantity
        ]);
    }

    public function removeFromCart($userId, $variantId)
    {
        $sql = "DELETE FROM {$this->table} 
                WHERE userId = :userId AND variantId = :variantId";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'userId' => $userId,
            'variantId' => $variantId
        ]);
    }

    public function clearCart($userId)
    {
        $sql = "DELETE FROM {$this->table} WHERE userId = :userId";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['userId' => $userId]);
    }

    public function getCartTotal($userId)
    {
        $sql = "SELECT SUM(pv.price * c.quantity) as total
                FROM {$this->table} c
                JOIN product_variants pv ON c.variantId = pv.id
                WHERE c.userId = :userId";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['userId' => $userId]);
        return $stmt->fetch()['total'] ?? 0;
    }

    public function getSelectedCartItems($userId, $variantIds)
    {
        $placeholders = str_repeat('?,', count($variantIds) - 1) . '?';
        
        $sql = "SELECT c.*, 
               p.id as productId,
               p.productName,
               p.salePercent,
               pv.price as originalPrice,
               pv.quantity as stockQuantity,
               CASE 
                   WHEN p.salePercent > 0 THEN pv.price * (1 - p.salePercent/100)
                   ELSE pv.price 
               END as finalPrice,
               pi.imageUrl
               FROM carts c
               JOIN product_variants pv ON c.variantId = pv.id
               JOIN products p ON pv.productId = p.id
               LEFT JOIN product_images pi ON pv.id = pi.variantId
               WHERE c.userId = ? AND c.variantId IN ($placeholders)";

        $stmt = $this->db->prepare($sql);
        $params = array_merge([$userId], $variantIds);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
} 