<?php

namespace App\Traits;

use App\Exceptions\ApiException;
use App\Models\ClothBuyItem;

trait ClothBuyTrait
{

    public function getItemsTransaction($cloth_buy_id, $inputs, $user): array
    {
        try {
            $company_id = $this->getCurrentCompanyOfUser($user);
            $db_color_ids = ClothBuyItem::select('color_id')
                ->where('cloth_buy_id', $cloth_buy_id)
                ->whereHas('cloth_buy.cloth', function ($q) use ($company_id) {
                    $q->where('company_id', $company_id);
                })
                ->pluck('color_id')
                ->toArray();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }

        $result['insert'] = [];
        $result['delete'] = [];
        $result['update'] = [];

        // insert
        foreach ($inputs['items'] as $item) {
            if (!in_array($item['color_id'], $db_color_ids)) {
                $result['insert'][] = [
                    'cloth_buy_id' => $cloth_buy_id,
                    'cloth_id' => $inputs['cloth_id'],
                    'color_id' => $item['color_id'],
                    'place_id' => $inputs['warehouse_place_id'],
                    'metre' => $item['metre'],
                    'unit_price' => $item['price'],
                    'price' => $item['price'],
                ];
            }
        }

        // delete
        $input_color_ids = array_column($inputs['items'], 'color_id');
        foreach ($db_color_ids as $db_color_id) {
            if (!in_array($db_color_id, $input_color_ids)) {
                $result['delete'][] = [
                    'cloth_buy_id' => $cloth_buy_id,
                    'color_id' => $db_color_id,
                ];
            }
        }

        // update
        foreach ($inputs['items'] as $item) {
            if (in_array($item['color_id'], $db_color_ids)) {
                $result['update'][] = [
                    'cloth_buy_id' => $cloth_buy_id,
                    'color_id' => $item['color_id'],
                    'metre' => $item['metre'],
                    'unit_price' => $item['price'],
                    'price' => $item['price'],
                ];
            }
        }

        return $result;
    }
}
