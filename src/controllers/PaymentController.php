<?php
namespace Controllers;

use Models\Payment;
use Models\Order;
use Exception;

class PaymentController {
    public function createPayment($orderId, $data) {
        try {
            // Validate dữ liệu thanh toán
            if (empty($data['paymentMethod'])) {
                throw new Exception("Vui lòng chọn phương thức thanh toán");
            }

            // Lấy thông tin đơn hàng
            $order = new Order(['id' => $orderId]);
            
            // Tạo payment mới
            $paymentData = [
                'amount' => $order->getTotalAmount(),
                'paymentMethod' => $data['paymentMethod']
            ];
            
            if ($data['paymentMethod'] === 'QR_TRANSFER') {
                // Tạo QR code và ref number cho thanh toán chuyển khoản
                $paymentData['qrImage'] = $this->generateQRCode($order);
                $paymentData['refNo'] = $this->generateRefNo();
            }

            $payment = new Payment($paymentData);
            
            // Lưu vào database
            
            return [
                'success' => true,
                'message' => 'Tạo thanh toán thành công',
                'data' => $payment->toArray()
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function verifyPayment($paymentId, $data) {
        try {
            $payment = new Payment(['id' => $paymentId]);
            
            // Xác thực thanh toán
            if ($payment->getPaymentMethod() === 'QR_TRANSFER') {
                // Kiểm tra ref number
                if ($data['refNo'] !== $payment->getRefNo()) {
                    throw new Exception("Mã thanh toán không hợp lệ");
                }
            }
            
            // Cập nhật trạng thái thanh toán
            $payment->updatePaymentStatus('COMPLETED');
            
            return [
                'success' => true,
                'message' => 'Xác thực thanh toán thành công'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    private function generateQRCode($order) {
        // Tạo nội dung QR code với thông tin chuyển khoản
        $qrContent = sprintf(
            "BK|%s|%s|%s",
            $order->getBankAccount(),
            $order->getTotalAmount(),
            $order->getId()
        );
        
        // Giả sử bạn đang sử dụng một thư viện QR code
        // Thay thế dòng dưới bằng thư viện QR thực tế của bạn
        return base64_encode($qrContent);
    }

    private function generateRefNo() {
        return uniqid('PAY', true);
    }
}
