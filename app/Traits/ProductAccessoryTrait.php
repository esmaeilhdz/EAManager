<?php

namespace App\Traits;


use App\Exceptions\ApiException;
use App\Models\Accessory;
use App\Models\AccessoryBuy;
use App\Models\Cloth;
use App\Models\ProductAccessory;

trait ProductAccessoryTrait
{
    use Common;

    private function preparingProductAccessoryDBData($product_accessories): array
    {
        $result = [];
        foreach ($product_accessories as $product_accessory) {
            if ($product_accessory->model_type == Cloth::class) {
                $result['cloth'][] = $product_accessory->model_id;
            } elseif ($product_accessory->model_type == Accessory::class) {
                $result['accessory'][] = $product_accessory->model_id;
            }
        }

        return $result;
    }

    private function preparingProductAccessoryInputsData($product_accessories): array
    {
        $result = [];

        foreach ($product_accessories as $key => $product_accessory) {
            if (!is_null($product_accessory['accessory_id'])) {
                $result['accessory'][$key]['id'] = intval($product_accessory['accessory_id']);
                $result['accessory'][$key]['amount'] = $product_accessory['amount'];
            } elseif (!is_null($product_accessory['cloth_code'])) {
                $cloth = Cloth::select('id')->whereCode($product_accessory['cloth_code'])->first();
                if (!$cloth) {
                    return [];
                }

                $result['cloth'][$key]['id'] = $cloth->id;
                $result['cloth'][$key]['amount'] = $product_accessory['amount'];
            }
        }

        return $result;
    }


    public function getInsertableAccessories($product_id, $inputs, $user): array
    {
        try {
            $company_id = $this->getCurrentCompanyOfUser($user);
            $product_accessories = ProductAccessory::select([
                    'id',
                    'model_type',
                    'model_id',
                ])
                ->whereHas('model', function ($q) use ($company_id) {
                    $q->where('company_id', $company_id);
                })
                ->where('product_id', $product_id)
                ->get();

            $db_product_accessories = $this->preparingProductAccessoryDBData($product_accessories);
            $inputs_product_accessories = $this->preparingProductAccessoryInputsData($inputs['product_accessories']);
        } catch (\Exception $e) {
            throw new ApiException($e);
        }

        $result = [];

        // insert
        if (isset($inputs_product_accessories['cloth'], $db_product_accessories['cloth'])) {
            foreach ($inputs_product_accessories['cloth'] as $key => $inputs_cloth) {
                if (!in_array($inputs_cloth['id'], $db_product_accessories['cloth'])) {
                    $result['inserts'][$key]['product_id'] = $product_id;
                    $result['inserts'][$key]['model_type'] = Cloth::class;
                    $result['inserts'][$key]['model_id'] = $inputs_cloth['id'];
                    $result['inserts'][$key]['amount'] = $inputs_cloth['amount'];
                }
            }
        }

        if (isset($inputs_product_accessories['accessory'], $db_product_accessories['accessory'])) {
            foreach ($inputs_product_accessories['accessory'] as $key => $inputs_accessory) {
                if (!in_array($inputs_accessory['id'], $db_product_accessories['accessory'])) {
                    $result['inserts'][$key]['product_id'] = $product_id;
                    $result['inserts'][$key]['model_type'] = Accessory::class;
                    $result['inserts'][$key]['model_id'] = $inputs_accessory['id'];
                    $result['inserts'][$key]['amount'] = $inputs_accessory['amount'];
                }
            }
        }

        // update
        if (isset($inputs_product_accessories['cloth'], $db_product_accessories['cloth'])) {
            foreach ($inputs_product_accessories['cloth'] as $key => $inputs_cloth) {
                if (in_array($inputs_cloth['id'], $db_product_accessories['cloth'])) {
                    $result['updates'][$key]['model_type'] = Cloth::class;
                    $result['updates'][$key]['model_id'] = $inputs_cloth['id'];
                    $result['updates'][$key]['amount'] = $inputs_cloth['amount'];
                }
            }
        }

        if (isset($inputs_product_accessories['accessory'], $db_product_accessories['accessory'])) {
            foreach ($inputs_product_accessories['accessory'] as $key => $inputs_accessory) {
                if (in_array($inputs_accessory['id'], $db_product_accessories['accessory'])) {
                    $result['updates'][$key]['model_type'] = Accessory::class;
                    $result['updates'][$key]['model_id'] = $inputs_accessory['id'];
                    $result['updates'][$key]['amount'] = $inputs_accessory['amount'];
                }
            }
        }

        // delete
        if (isset($inputs_product_accessories['cloth'])) {
            $inputs_cloth_ids = array_column($inputs_product_accessories['cloth'], 'id');
            foreach ($db_product_accessories['cloth'] as $key => $db_cloth_id) {
                if (!in_array($db_cloth_id, $inputs_cloth_ids)) {
                    $result['deletes'][$key]['model_type'] = Cloth::class;
                    $result['deletes'][$key]['model_id'] = $db_cloth_id;
                }
            }
        }

        if (isset($inputs_product_accessories['accessory'])) {
            $inputs_accessory_ids = array_column($inputs_product_accessories['accessory'], 'id');
            foreach ($db_product_accessories['accessory'] as $key => $db_accessory_id) {
                if (!in_array($db_accessory_id, $inputs_accessory_ids)) {
                    $result['deletes'][$key]['model_type'] = Accessory::class;
                    $result['deletes'][$key]['model_id'] = $db_accessory_id;
                }
            }
        }

        return $result;
    }

}
