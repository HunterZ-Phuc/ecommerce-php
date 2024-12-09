<?php

namespace App\Controllers;

use App\Models\VariantCombination;

class VariantCombinationController extends BaseController
{
    private $combinationModel;

    public function __construct()
    {
        $this->combinationModel = new VariantCombination();
    }

    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'variantId' => $_POST['variantId'],
                'variantTypeId' => $_POST['variantTypeId'],
                'variantValueId' => $_POST['variantValueId']
            ];
            $id = $this->combinationModel->create($data);
            return $this->json(['id' => $id]);
        }
    }

    public function delete($id)
    {
        $this->combinationModel->delete($id);
        return $this->json(['success' => true]);
    }
}