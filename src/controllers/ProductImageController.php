<?php
namespace Controllers;

use Models\ProductImage;
use Exception;

class ProductImageController {
    public function uploadImage($productId, $data) {
        try {
            // Validate file ảnh
            if (empty($_FILES['image'])) {
                throw new Exception("Vui lòng chọn ảnh");
            }

            // Xử lý upload file
            $imageUrl = $this->handleFileUpload($_FILES['image']);
            
            // Tạo record mới
            $imageData = [
                'productId' => $productId,
                'variantId' => $data['variantId'] ?? null,
                'imageUrl' => $imageUrl,
                'isThumbnail' => $data['isThumbnail'] ?? false,
                'displayOrder' => $data['displayOrder'] ?? 0
            ];
            
            $productImage = new ProductImage($imageData);
            
            // Lưu vào database
            
            return [
                'success' => true,
                'message' => 'Upload ảnh thành công',
                'data' => $productImage->toArray()
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function updateImageInfo($imageId, $data) {
        try {
            $image = new ProductImage(['id' => $imageId]);
            
            if (isset($data['isThumbnail'])) {
                $image->setIsThumbnail($data['isThumbnail']);
            }
            if (isset($data['displayOrder'])) {
                $image->setDisplayOrder($data['displayOrder']);
            }
            
            // Lưu vào database
            
            return [
                'success' => true,
                'message' => 'Cập nhật thông tin ảnh thành công'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    private function handleFileUpload($file) {
        // Logic xử lý upload file
        // Kiểm tra mime type
        // Tạo tên file ngẫu nhiên
        // Di chuyển file vào thư mục uploads
        return "path/to/uploaded/image.jpg";
    }
} 