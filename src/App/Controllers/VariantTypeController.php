<?php

namespace App\Controllers;

use App\Models\VariantType;

class VariantTypeController extends BaseController
{
    private $variantTypeModel;

    public function __construct()
    {
        $this->variantTypeModel = new VariantType();
    }

    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'productId' => $_POST['productId'],
                'name' => $_POST['name']
            ];
            $typeId = $this->variantTypeModel->create($data);
            return $this->json(['id' => $typeId]);
        }
    }

    public function delete($id)
    {
        $this->variantTypeModel->delete($id);
        return $this->json(['success' => true]);
    }
} 