<?php

namespace App\Controllers;

use App\Models\Item;

class ItemController extends BaseController
{
    private $itemModel;

    public function __construct()
    {
        $this->itemModel = new Item();
    }

    public function getByOrderId($orderId)
    {
        $items = $this->itemModel->findByOrderId($orderId);
        return $this->json($items);
    }

    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'orderId' => $_POST['orderId'],
                'productId' => $_POST['productId'],
                'variantId' => $_POST['variantId'] ?? null,
                'quantity' => $_POST['quantity'],
                'price' => $_POST['price']
            ];

            $itemId = $this->itemModel->create($data);
            return $this->json(['id' => $itemId]);
        }
    }
}