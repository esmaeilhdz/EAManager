<?php

namespace App\Helpers;

use App\Exceptions\ApiException;
use App\Repositories\Interfaces\iCustomer;
use App\Repositories\Interfaces\iFactor;
use App\Repositories\Interfaces\iFactorPayment;
use App\Repositories\Interfaces\iFactorProduct;
use App\Repositories\Interfaces\iProductWarehouse;
use App\Repositories\Interfaces\iRequestProductWarehouse;
use App\Service\Factor\CheckFactor;
use App\Traits\Common;
use App\Traits\FactorTrait;
use App\Traits\RequestProductWarehouseTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FactorHelper
{
    use Common, RequestProductWarehouseTrait, FactorTrait;

    // فاکتور ناقص
    const InCompleteFactor = 1;
    // فاکتور تایید شده
    const ConfirmFactor = 2;
    // فاکتور مرجوعی
    const ReturnedFactor = 3;

    // attributes
    public iFactor $factor_interface;
    public iCustomer $customer_interface;
    public iFactorProduct $factor_product_interface;
    public iFactorPayment $factor_payment_interface;
    public iProductWarehouse $product_warehouse_interface;
    public iRequestProductWarehouse $request_product_interface;

    public function __construct(
        iFactor                  $factor_interface,
        iCustomer                $customer_interface,
        iFactorProduct           $factor_product_interface,
        iFactorPayment           $factor_payment_interface,
        iProductWarehouse        $product_warehouse_interface,
        iRequestProductWarehouse $request_product_interface,
    )
    {
        $this->factor_interface = $factor_interface;
        $this->customer_interface = $customer_interface;
        $this->factor_product_interface = $factor_product_interface;
        $this->factor_payment_interface = $factor_payment_interface;
        $this->product_warehouse_interface = $product_warehouse_interface;
        $this->request_product_interface = $request_product_interface;
    }

    /**
     * لیست فاکتور ها
     * @param $inputs
     * @return array
     */
    public function getFactors($inputs): array
    {
        $search_data = $param_array = [];
        $search_data[] = $this->GWC($inputs['search_txt'] ?? '', 'string:factor_no');
        $inputs['where']['search']['condition'] = $this->generateWhereCondition($search_data, $param_array);
        $inputs['where']['search']['params'] = $param_array;

        $search_data = $param_array = [];
        $search_data[] = $this->GWC($inputs['search_txt'] ?? '', 'string:name;mobile');
        $inputs['where']['customer']['condition'] = $this->generateWhereCondition($search_data, $param_array);
        $inputs['where']['customer']['params'] = $param_array;

        $inputs['order_by'] = $this->orderBy($inputs, 'factors');
        $inputs['per_page'] = $this->calculatePerPage($inputs);

        $factors = $this->factor_interface->getFactors($inputs);

        $factors->transform(function ($item) {
            return [
                'code' => $item->code,
                'customer' => [
                    'name' => $item->customer->name,
                    'mobile' => $item->customer->mobile,
                ],
                'factor_no' => $item->factor_no,
                'settlement_date' => $item->settlement_date,
                'has_return_permission' => $item->has_return_permission,
                'is_credit' => $item->is_credit,
                'status' => [
                    'id' => $item->status,
                    'caption' => $item->factor_status->enum_caption
                ],
                'final_price' => $item->final_price,
                'creator' => is_null($item->creator->person) ? null : [
                    'person' => [
                        'full_name' => $item->creator->person->name . ' ' . $item->creator->person->family,
                    ]
                ],
                'created_at' => $item->created_at,
            ];
        });

        return [
            'result' => true,
            'message' => __('messages.success'),
            'data' => $factors
        ];
    }

    /**
     * لیست فاکتورهای قابل بستن
     * @param $inputs
     * @return array
     */
    public function getCompletableFactors($inputs): array
    {
        $search_data = $param_array = [];
        $search_data[] = $this->GWC($inputs['search_txt'] ?? '', 'string:factor_no');
        $inputs['where']['search']['condition'] = $this->generateWhereCondition($search_data, $param_array);
        $inputs['where']['search']['params'] = $param_array;

        $search_data = $param_array = [];
        $search_data[] = $this->GWC($inputs['search_txt'] ?? '', 'string:name;mobile');
        $inputs['where']['customer']['condition'] = $this->generateWhereCondition($search_data, $param_array);
        $inputs['where']['customer']['params'] = $param_array;

        $inputs['order_by'] = $this->orderBy($inputs, 'factors');
        $inputs['per_page'] = $this->calculatePerPage($inputs);

        $factors = $this->factor_interface->getCompletableFactors($inputs);

        $factors->transform(function ($item) {
            return [
                'code' => $item->code,
                'customer' => [
                    'name' => $item->customer->name,
                    'mobile' => $item->customer->mobile,
                ],
                'factor_no' => $item->factor_no,
                'settlement_date' => $item->settlement_date,
                'has_return_permission' => $item->has_return_permission,
                'is_credit' => $item->is_credit,
                'status' => $item->status,
                'final_price' => $item->final_price,
                'creator' => is_null($item->creator->person) ? null : [
                    'person' => [
                        'full_name' => $item->creator->person->name . ' ' . $item->creator->person->family,
                    ]
                ],
                'created_at' => $item->created_at,
            ];
        });

        return [
            'result' => true,
            'message' => __('messages.success'),
            'data' => $factors
        ];
    }

    /**
     * جزئیات فاکتور
     * @param $code
     * @return array
     */
    public function getFactorDetail($code): array
    {
        $user = Auth::user();
        $select = ['id', 'code', 'customer_id', 'factor_no', 'has_return_permission', 'is_credit', 'status', 'settlement_date', 'final_price'];
        $relation = [
            'customer:id,name,mobile,score',
            'factor_products:factor_id,product_warehouse_id,free_size_count,size1_count,size2_count,size3_count,size4_count,price',
            'factor_products.product_warehouse:id,product_id,product_model_id',
            'factor_products.product_warehouse.product:id,name',
            'factor_products.product_warehouse.product_model:id,product_id,name',
            'factor_payments:factor_id,payment_type_id,description,price',
            'factor_payments.payment_type:enum_id,enum_caption',
        ];
        $factor = $this->factor_interface->getFactorByCode($code, $user, $select, $relation);
        if (is_null($factor)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $factor_products = [];
        foreach ($factor->factor_products as $factor_product) {
            $factor_products[] = [
                'free_size_count' => $factor_product->free_size_count,
                'size1_count' => $factor_product->size1_count,
                'size2_count' => $factor_product->size2_count,
                'size3_count' => $factor_product->size3_count,
                'size4_count' => $factor_product->size4_count,
                'price' => $factor_product->price,
                'product' => $factor_product->product_warehouse->product->name . ' - ' . $factor_product->product_warehouse->product_model->name
            ];
        }

        $factor_payments = [];
        foreach ($factor->factor_payments as $factor_payment) {
            $factor_payments[] = [
                'payment_type' => [
                    'id' => $factor_payment->payment_type_id,
                    'caption' => $factor_payment->payment_type->enum_caption
                ],
                'description' => $factor_payment->description,
                'price' => $factor_payment->price,
            ];
        }

        $factor = $factor->toArray();
        $factor['factor_products'] = $factor_products;
        $factor['factor_payments'] = $factor_payments;

        return [
            'result' => true,
            'message' => __('messages.success'),
            'data' => $factor
        ];
    }

    /**
     * ویرایش فاکتور
     * @param $inputs
     * @return array
     */
    public function editFactor($inputs): array
    {
        // فاکتور
        $user = Auth::user();
        $factor = $this->factor_interface->getFactorByCode($inputs['code'], $user, ['id']);
        if (is_null($factor)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $check_factor = new CheckFactor();
        $result = $check_factor->CheckForChangeProduct($factor);
        if (!$result['result']) {
            return $result;
        }

        // مشتری
        $select = ['id'];
        $customer = $this->customer_interface->getCustomerByCode($inputs['customer_code'], $user, $select);
        if (is_null($customer)) {
            return [
                'result' => false,
                'message' => __('messages.customer_not_found'),
                'data' => null
            ];
        }
        $inputs['customer_id'] = $customer->id;

        // ویرایش فاکتور
        $result = $this->factor_interface->editFactor($factor, $inputs);

        return [
            'result' => $result,
            'message' => $result ? __('messages.success') : __('messages.fail'),
            'data' => null
        ];
    }

    /**
     * تغییر وضعیت فاکتور
     * @param $inputs
     * @return array
     */
    public function changeStatusFactor($inputs): array
    {
        $user = Auth::user();
        // فاکتور
        $select = ['id', 'status', 'has_return_permission'];
        $factor = $this->factor_interface->getFactorByCode($inputs['code'], $user, $select);
        if (is_null($factor)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        if ($factor->status == self::ConfirmFactor && $inputs['status'] == self::ConfirmFactor) {
            return [
                'result' => false,
                'message' => __('messages.factor_already_confirmed'),
                'data' => null
            ];
        }

        DB::beginTransaction();
        $result[] = $this->factor_interface->changeStatusFactor($factor, $inputs);

        // محاسبه برای ویرایش آمار انبار
        $change_status_data = $this->changeStatusFactorHelper($inputs['status'], $factor);
        if (!$change_status_data['result']) {
            return $change_status_data;
        }

        foreach ($change_status_data['data']['params'] as $key => $param) {
            if (isset($param['sign'])) {
                if ($param['sign'] == 'plus') {
                    $param['free_size_count'] = $change_status_data['data']['factor_products'][$key]->product_warehouse->free_size_count + $param['free_size_count'];
                    $param['size1_count'] = $change_status_data['data']['factor_products'][$key]->product_warehouse->size1_count + $param['size1_count'];
                    $param['size2_count'] = $change_status_data['data']['factor_products'][$key]->product_warehouse->size2_count + $param['size2_count'];
                    $param['size3_count'] = $change_status_data['data']['factor_products'][$key]->product_warehouse->size3_count + $param['size3_count'];
                    $param['size4_count'] = $change_status_data['data']['factor_products'][$key]->product_warehouse->size4_count + $param['size4_count'];
                } elseif ($param['sign'] == 'minus') {
                    $param['free_size_count'] = $change_status_data['data']['factor_products'][$key]->product_warehouse->free_size_count - $param['free_size_count'];
                    $param['size1_count'] = $change_status_data['data']['factor_products'][$key]->product_warehouse->size1_count - $param['size1_count'];
                    $param['size2_count'] = $change_status_data['data']['factor_products'][$key]->product_warehouse->size2_count - $param['size2_count'];
                    $param['size3_count'] = $change_status_data['data']['factor_products'][$key]->product_warehouse->size3_count - $param['size3_count'];
                    $param['size4_count'] = $change_status_data['data']['factor_products'][$key]->product_warehouse->size4_count - $param['size4_count'];
                }
            }
            $result[] = $this->product_warehouse_interface->editProductWarehouse($change_status_data['data']['factor_products'][$key]->product_warehouse, $param);
        }

        if (!in_array(false, $result)) {
            $flag = true;
            DB::commit();
        } else {
            $flag = false;
            DB::rollBack();
        }

        return [
            'result' => $flag,
            'message' => $flag ? __('messages.success') : __('messages.fail'),
            'data' => null
        ];
    }

    /**
     * افزودن فاکتور
     * @param $inputs
     * @return array
     */
    public function addFactor($inputs): array
    {
        $user = Auth::user();

        $select = ['id'];
        $customer = $this->customer_interface->getCustomerByCode($inputs['customer_code'], $user, $select);
        if (is_null($customer)) {
            return [
                'result' => false,
                'message' => __('messages.customer_not_found'),
                'data' => null
            ];
        }
        $inputs['customer_id'] = $customer->id;

        $res_factor = $this->factor_interface->addFactor($inputs, $user);
        $factor_result = $res_factor['result'];
        $factor_data = $res_factor['data'];

        return [
            'result' => $factor_result,
            'message' => $factor_result ? __('messages.success') : __('messages.fail'),
            'data' => $factor_data->code ?? null
        ];
    }

    /**
     * حذف فاکتور
     * @param $code
     * @return array
     */
    public function deleteFactor($code): array
    {
        $user = Auth::user();
        $factor = $this->factor_interface->getFactorByCode($code, $user, ['id']);
        if (is_null($factor)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $check_factor = new CheckFactor();
        $result = $check_factor->CheckForChangeProduct($factor);
        if (!$result['result']) {
            return $result;
        }

        DB::beginTransaction();
        $result[] = $this->factor_interface->deleteFactor($factor);
        $result[] = (bool)$this->factor_product_interface->deleteFactorProducts($factor->id);
        $result[] = (bool)$this->factor_payment_interface->deleteFactorPayments($factor->id);

        if (!in_array(false, $result)) {
            $flag = true;
            DB::commit();
        } else {
            $flag = false;
            DB::rollBack();
        }

        return [
            'result' => $flag,
            'message' => $flag ? __('messages.success') : __('messages.fail'),
            'data' => null
        ];
    }

}
