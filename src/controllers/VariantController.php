<?php
namespace Controllers;

use Models\VariantType;
use Models\VariantValue;
use Exception;

class VariantController {
    public function createVariantType($productId, $data) {
        try {
            if (empty($data['name'])) {
                throw new Exception("Tên loại biến thể không được để trống");
            }

            $variantType = new VariantType([
                'productId' => $productId,
                'name' => $data['name']
            ]);

            // Thêm các giá trị cho biến thể
            if (!empty($data['values'])) {
                foreach ($data['values'] as $value) {
                    $variantValue = new VariantValue([
                        'value' => $value
                    ]);
                    $variantType->addValue($variantValue);
                }
            }
            
            // Lưu vào database

            return [
                'success' => true,
                'message' => 'Tạo biến thể thành công',
                'data' => $variantType->toArray()
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function updateVariantType($typeId, $data) {
        try {
            $variantType = new VariantType(['id' => $typeId]);
            
            if (isset($data['name'])) {
                $variantType->setName($data['name']);
            }
            
            // Lưu vào database
            
            return [
                'success' => true,
                'message' => 'Cập nhật biến thể thành công'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
} 