<?php
namespace Controllers;

use Models\Cart;
use Models\ProductVariant;
use Exception;

class CartController {
    public function addToCart($userId, $data) {
        try {
            // Validate dữ liệu
            if (empty($data['variantId']) || empty($data['quantity'])) {
                throw new Exception("Thiếu thông tin sản phẩm");
            }

            // Kiểm tra variant tồn tại và còn hàng
            $variant = new ProductVariant(['id' => $data['variantId']]);
            if (!$variant->isInStock()) {
                throw new Exception("Sản phẩm đã hết hàng");
            }

            // Kiểm tra số lượng hợp lệ
            if ($data['quantity'] > $variant->getQuantity()) {
                throw new Exception("Số lượng vượt quá hàng tồn kho");
            }

            // Tạo hoặc cập nhật cart item
            $cartItem = new Cart([
                'userId' => $userId,
                'variantId' => $data['variantId'],
                'quantity' => $data['quantity']
            ]);
            $cartItem->setVariant($variant);

            // Lưu vào database
            
            return [
                'success' => true,
                'message' => 'Thêm vào giỏ hàng thành công',
                'data' => $cartItem->toArray()
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function updateCartItem($cartId, $data) {
        try {
            $cartItem = new Cart(['id' => $cartId]);
            
            if (isset($data['quantity'])) {
                // Kiểm tra số lượng tồn kho
                if ($data['quantity'] > $cartItem->getVariant()->getQuantity()) {
                    throw new Exception("Số lượng vượt quá hàng tồn kho");
                }
                $cartItem->updateQuantity($data['quantity']);
            }
            
            // Lưu vào database
            
            return [
                'success' => true,
                'message' => 'Cập nhật giỏ hàng thành công',
                'data' => $cartItem->toArray()
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function getCart($userId) {
        try {
            // Logic lấy giỏ hàng của user
            $cartItems = []; // Query từ database
            
            $total = array_reduce($cartItems, function($sum, $item) {
                return $sum + $item->getSubtotal();
            }, 0);
            
            return [
                'success' => true,
                'data' => [
                    'items' => array_map(fn($item) => $item->toArray(), $cartItems),
                    'total' => $total
                ]
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
}