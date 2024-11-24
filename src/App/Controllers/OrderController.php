<?php

namespace App\Controllers;

use App\Models\Order;
use App\Models\Item;
use App\Models\Payment;

class OrderController extends BaseController
{
    private $orderModel;
    private $itemModel;
    private $paymentModel;

    public function __construct()
    {
        $this->orderModel = new Order();
        $this->itemModel = new Item();
        $this->paymentModel = new Payment();
    }

    public function index()
    {
        $orders = $this->orderModel->findAll();
        $this->view([
            'title' => 'Quản lý Đơn hàng',
            'orders' => $orders
        ]);
    }

    public function viewOrder($id)
    {
        $order = $this->orderModel->findById($id);
        $items = $this->itemModel->findByOrderId($id);
        $payment = $this->paymentModel->findByOrderId($id);

        $this->view([
            'title' => 'Chi tiết Đơn hàng',
            'order' => $order,
            'items' => $items,
            'payment' => $payment
        ]);
    }

    public function updateStatus($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $status = $_POST['status'];
            $this->orderModel->updateStatus($id, $status);
            
            if ($status === 'DELIVERED') {
                $payment = $this->paymentModel->findByOrderId($id);
                $this->paymentModel->updateStatus($payment['id'], 'COMPLETED');
            }

            header('Location: /php-mvc/employee/order-management');
            exit;
        }
    }
} 