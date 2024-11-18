<?php
namespace Controllers;

use Models\Order;
use Models\Product;
use Exception;

class StatisticsController {
    public function getRevenueStats($filters = []) {
        try {
            // Logic tính toán doanh thu
            $stats = [
                'totalRevenue' => 0,
                'totalOrders' => 0,
                'averageOrderValue' => 0,
                'revenueByDate' => [],
                'revenueByCategory' => []
            ];

            // Lấy tất cả đơn hàng đã hoàn thành
            $completedOrders = []; // Query từ database với filter
            
            foreach ($completedOrders as $order) {
                $stats['totalRevenue'] += $order->getTotalAmount();
                $stats['totalOrders']++;
                
                // Thống kê theo ngày
                $date = date('Y-m-d', strtotime($order->getOrderDate()));
                if (!isset($stats['revenueByDate'][$date])) {
                    $stats['revenueByDate'][$date] = 0;
                }
                $stats['revenueByDate'][$date] += $order->getTotalAmount();
                
                // Thống kê theo danh mục
                foreach ($order->getItems() as $item) {
                    $category = $item->getProduct()->getCategory();
                    if (!isset($stats['revenueByCategory'][$category])) {
                        $stats['revenueByCategory'][$category] = 0;
                    }
                    $stats['revenueByCategory'][$category] += $item->getSubtotal();
                }
            }
            
            // Tính giá trị trung bình đơn hàng
            if ($stats['totalOrders'] > 0) {
                $stats['averageOrderValue'] = $stats['totalRevenue'] / $stats['totalOrders'];
            }

            return [
                'success' => true,
                'data' => $stats
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function getProductStats() {
        try {
            $stats = [
                'totalProducts' => 0,
                'topSelling' => [],
                'lowStock' => [],
                'outOfStock' => []
            ];
            
            // Lấy thống kê sản phẩm
            $products = []; // Query từ database
            
            foreach ($products as $product) {
                $stats['totalProducts']++;
                
                // Sản phẩm bán chạy
                if ($product->getSold() > 100) {
                    $stats['topSelling'][] = $product->toArray();
                }
                
                // Sản phẩm sắp hết hàng
                if ($product->getStockQuantity() < 10 && $product->getStockQuantity() > 0) {
                    $stats['lowStock'][] = $product->toArray();
                }
                
                // Sản phẩm hết hàng
                if ($product->getStockQuantity() == 0) {
                    $stats['outOfStock'][] = $product->toArray();
                }
            }

            return [
                'success' => true,
                'data' => $stats
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
} 