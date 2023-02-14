<?php

namespace App\Traits;

trait SewingTrait
{

    /**
     * محاسبه تعداد دوخت برای ویرایش تعداد کالاهای انبار
     * @param $sewing
     * @param $inputs
     * @return array
     */
    public function calculateForProductWarehouse($sewing, $inputs): array
    {
        if ($inputs['count'] > $sewing->count) {
            $params['free_size_count'] = $inputs['count'] - $sewing->count;
            $params['size1_count'] = $inputs['count'] - $sewing->count;
            $params['size2_count'] = $inputs['count'] - $sewing->count;
            $params['size3_count'] = $inputs['count'] - $sewing->count;
            $params['size4_count'] = $inputs['count'] - $sewing->count;
            $params['sign'] = 'plus';
        } elseif ($inputs['count'] < $sewing->count) {
            $params['free_size_count'] = $sewing->count - $inputs['count'];
            $params['size1_count'] = $sewing->count - $inputs['count'];
            $params['size2_count'] = $sewing->count - $inputs['count'];
            $params['size3_count'] = $sewing->count - $inputs['count'];
            $params['size4_count'] = $sewing->count - $inputs['count'];
            $params['sign'] = 'minus';
        } else {
            $params['free_size_count'] = $sewing->count;
            $params['size1_count'] = $sewing->count;
            $params['size2_count'] = $sewing->count;
            $params['size3_count'] = $sewing->count;
            $params['size4_count'] = $sewing->count;
            $params['sign'] = 'equal';
        }

        return $params;
    }

}
