<?php
namespace App\Helpers;
// Sửa 1
class OrderHelper
{
    public static function getOrderStatusText($status)
    {
        switch ($status) {
            case 'PENDING':
                return 'Chờ xử lý';
            case 'PROCESSING':
                return 'Đang xử lý';
            case 'CONFIRMED':
                return 'Đã xác nhận';
            case 'READY_FOR_SHIPPING':
                return 'Sẵn sàng giao hàng';
            case 'SHIPPING':
                return 'Đang giao hàng';
            case 'SHIPPED':
                return 'Đã giao';
            case 'DELIVERED':
                return 'Đã giao hàng';
            case 'RETURN_REQUEST':
                return 'Yêu cầu hoàn trả';
            case 'RETURN_APPROVED':
                return 'Chấp nhận hoàn trả';
            case 'RETURNED':
                return 'Đã hoàn trả';
            case 'CANCELLED':
                return 'Đã hủy';
            default:
                return 'Không xác định';
        }
    }

    public static function getOrderStatusClass($status)
    {
        switch ($status) {
            case 'PENDING':
                return 'bg-warning';
            case 'PROCESSING':
            case 'CONFIRMED':
                return 'bg-info';
            case 'SHIPPING':
                return 'bg-primary';
            case 'DELIVERED':
                return 'bg-success';
            case 'RETURN_REQUEST':
            case 'RETURN_APPROVED':
                return 'bg-warning';
            case 'RETURNED':
                return 'bg-info';
            case 'CANCELLED':
                return 'bg-danger';
            default:
                return 'bg-secondary';
        }
    }

    public static function getPaymentStatusText($status)
    {
        switch ($status) {
            case 'PENDING':
                return 'Chờ thanh toán';
            case 'PAID':
                return 'Đã thanh toán';
            case 'REFUNDED':
                return 'Đã hoàn tiền';
            default:
                return 'Không xác định';
        }
    }

    public static function getPaymentStatusClass($status)
    {
        switch ($status) {
            case 'PENDING':
                return 'bg-warning';
            case 'PAID':
                return 'bg-success';
            case 'REFUNDED':
                return 'bg-info';
            default:
                return 'bg-secondary';
        }
    }
}