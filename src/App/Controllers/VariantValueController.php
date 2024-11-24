<?php

namespace App\Controllers;

use App\Models\VariantValue;

class VariantValueController extends BaseController
{
    private $valueModel;

    public function __construct()
    {
        $this->valueModel = new VariantValue();
    }

    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'variantTypeId' => $_POST['variantTypeId'],
                'value' => $_POST['value']
            ];
            $id = $this->valueModel->create($data);
            return $this->json(['id' => $id]);
        }
    }

    public function update($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'value' => $_POST['value']
            ];
            $this->valueModel->update($id, $data);
            return $this->json(['success' => true]);
        }
    }

    public function delete($id)
    {
        $this->valueModel->delete($id);
        return $this->json(['success' => true]);
    }
} 