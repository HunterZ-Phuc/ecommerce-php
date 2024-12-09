<?php

namespace App\Controllers;

use App\Models\ProductVariant;

class ProductVariantController extends BaseController
{
    private $variantModel;

    public function __construct()
    {
        $this->variantModel = new ProductVariant();
    }

    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'productId' => $_POST['productId'],
                'variantName' => $_POST['variantName'],
                'quantity' => $_POST['quantity'],
                'price' => $_POST['price']
            ];
            $this->variantModel->create($data);
            header('Location: /ecommerce-php/employee/product-management');
            exit;
        }
    }

    public function edit($id)
    {
        $variant = $this->variantModel->findById($id);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'variantName' => $_POST['variantName'],
                'quantity' => $_POST['quantity'],
                'price' => $_POST['price']
            ];
            $this->variantModel->update($id, $data);
            header('Location: /ecommerce-php/employee/product-management');
            exit;
        }

        $this->view('employee/ProductManagement/edit_variant', [
            'title' => 'Sửa Biến thể',
            'variant' => $variant
        ]);
    }

    public function delete($id)
    {
        $this->variantModel->delete($id);
        header('Location: /ecommerce-php/employee/product-management');
        exit;
    }
}