<?php

namespace App\Controllers;

use App\Models\Payment;

class PaymentController extends BaseController
{
    private $paymentModel;

    public function __construct()
    {
        $this->paymentModel = new Payment();
    }

    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'orderId' => $_POST['orderId'],
                'amount' => $_POST['amount'],
                'paymentMethod' => $_POST['paymentMethod']
            ];

            if ($data['paymentMethod'] === 'QR_TRANSFER') {
                $data['qrImage'] = $this->generateQRCode($data['amount']);
            }

            $paymentId = $this->paymentModel->create($data);
            return $this->json(['id' => $paymentId]);
        }
    }

    public function updateStatus($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $status = $_POST['status'];
            $this->paymentModel->updateStatus($id, $status);
            return $this->json(['success' => true]);
        }
    }

    private function generateQRCode($amount)
    {
        // Logic tạo mã QR cho thanh toán
        return 'qr_code_' . time() . '.png';
    }
}