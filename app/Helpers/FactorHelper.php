<?php

namespace App\Helpers;

use App\Facades\ProductWarehouseFacade;
use App\Repositories\Interfaces\iCustomer;
use App\Repositories\Interfaces\iFactor;
use App\Repositories\Interfaces\iFactorPayment;
use App\Repositories\Interfaces\iFactorProduct;
use App\Repositories\Interfaces\iProductWarehouse;
use App\Repositories\Interfaces\iRequestProductWarehouse;
use App\Traits\Common;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FactorHelper
{
    use Common;

    // attributes
    public iFactor $factor_interface;
    public iFactorProduct $factor_product_interface;
    public iFactorPayment $factor_payment_interface;
    public iCustomer $customer_interface;
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

        $search_data = $param_array = [];
        $search_data[] = $this->GWC($inputs['sale_period_id'] ?? '', 'string:name');
        $inputs['where']['sale_period']['condition'] = $this->generateWhereCondition($search_data, $param_array);
        $inputs['where']['sale_period']['params'] = $param_array;

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
                'sale_period' => [
                    'id' => $item->sale_period_id,
                    'caption' => $item->sale_period->name,
                ],
                'factor_no' => $item->factor_no,
                'settlement_date' => $item->settlement_date,
                'has_return_permission' => $item->has_return_permission,
                'is_credit' => $item->is_credit,
                'is_complete' => $item->is_complete,
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
        $select = ['id', 'code', 'customer_id', 'sale_period_id', 'factor_no', 'has_return_permission', 'is_credit', 'is_complete', 'settlement_date', 'final_price'];
        $relation = [
            'customer:id,name,mobile,score',
            'factor_products:factor_id,product_warehouse_id,free_size_count,size1_count,size2_count,size3_count,size4_count,price',
            'factor_products.product_warehouse:id,product_id',
            'factor_products.product_warehouse.product:id,name',
            'factor_payments:factor_id,payment_type_id,description,price',
            'factor_payments.payment_type:enum_id,enum_caption',
        ];
        $factor = $this->factor_interface->getFactorByCode($code, $select, $relation);
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
                'product' => $factor_product->product_warehouse->product->name
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
        $factor = $this->factor_interface->getFactorByCode($inputs['code'], ['id']);
        if (is_null($factor)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        // مشتری
        $select = ['id'];
        $customer = $this->customer_interface->getCustomerByCode($inputs['customer_code'], $select);
        if (is_null($customer)) {
            return [
                'result' => false,
                'message' => __('messages.customer_not_found'),
                'data' => null
            ];
        }
        $inputs['customer_id'] = $customer->id;

        DB::beginTransaction();
        // ویرایش فاکتور
        $result[] = $this->factor_interface->editFactor($factor, $inputs);

        // ویرایش محصولات فاکتور
        foreach ($inputs['products'] as $product_item) {
            $factor_product = $this->factor_product_interface->getById($factor->id, $product_item['id']);
            if (is_null($factor_product)) {
                return [
                    'result' => false,
                    'message' => __('messages.factor_product_not_found'),
                    'data' => null
                ];
            }
            $result[] = $this->factor_product_interface->editFactorProduct($factor_product, $product_item);
        }

        // ویرایش پرداخت های فاکتور
        foreach ($inputs['payments'] as $payment_item) {
            $factor_payment = $this->factor_payment_interface->getById($factor->id, $payment_item['id']);
            if (is_null($factor_payment)) {
                return [
                    'result' => false,
                    'message' => __('messages.factor_product_not_found'),
                    'data' => null
                ];
            }
            $result[] = $this->factor_payment_interface->editFactorPayment($factor_payment, $payment_item);
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

    public function changeCompleteFactor($inputs): array
    {
        // فاکتور
        $select = ['id', 'is_complete'];
        $factor = $this->factor_interface->getFactorByCode($inputs['code'], $select);
        if (is_null($factor)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        DB::beginTransaction();
        $result[] = $this->factor_interface->changeCompleteFactor($factor, $inputs);
        // تکمیل فاکتور ناقص
        // باید موجودی انبار موردنظر به تعداد خریداری شده، کسر شود.
        if ($inputs['is_complete'] == 1) {
            $select = ['product_warehouse_id', 'free_size_count', 'size1_count', 'size2_count', 'size3_count', 'size4_count'];
            $relation = [
                'product_warehouse:id,free_size_count,size1_count,size2_count,size3_count,size4_count'
            ];
            $factor_products = $this->factor_product_interface->getByFactorId($factor->id, $select, $relation);
            foreach ($factor_products as $factor_product) {
                $params['sign'] = 'minus';
                $params['free_size_count'] = $factor_product->free_size_count;
                $params['size1_count'] = $factor_product->size1_count;
                $params['size2_count'] = $factor_product->size2_count;
                $params['size3_count'] = $factor_product->size3_count;
                $params['size4_count'] = $factor_product->size4_count;
                $result[] = $this->product_warehouse_interface->editProductWarehouse($factor_product->product_warehouse, $params);
            }
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
     * @throws \App\Exceptions\ApiException
     */
    public function addFactor($inputs): array
    {
        $select = ['id'];
        $customer = $this->customer_interface->getCustomerByCode($inputs['customer_code'], $select);
        if (is_null($customer)) {
            return [
                'result' => false,
                'message' => __('messages.customer_not_found'),
                'data' => null
            ];
        }
        $inputs['customer_id'] = $customer->id;

        $user = Auth::user();
        DB::beginTransaction();
        $res_factor = $this->factor_interface->addFactor($inputs, $user);
        $result[] = $res_factor['result'];

        $company_id = $this->getCurrentCompanyOfUser($user);
        foreach ($inputs['products'] as $product_item) {

            $res = $this->factor_product_interface->addFactorProduct($product_item, $res_factor['data']['id'], $user);
            $result[] = $res['result'];
            // باید موجودی انبار موردنظر به تعداد خریداری شده، کسر شود.
            $select = ['id', 'product_id', 'free_size_count', 'size1_count', 'size2_count', 'size3_count', 'size4_count'];
            $product_warehouse_primary = $this->product_warehouse_interface->getById($product_item['product_warehouse_id'], $select);
            // بررسی موجودی انبار با تعداد فاکتور
            $result_check_stock = ProductWarehouseFacade::checkStock($product_warehouse_primary, $product_item);
            if ($inputs['is_complete'] == 1) {

                if (!$result_check_stock['result']) {
                    return [
                        'result' => false,
                        'message' => $result_check_stock['message'],
                        'data' => $result_check_stock['data']
                    ];
                }

                $params['sign'] = 'minus';
                $params['free_size_count'] = $product_item['free_size_count'];
                $params['size1_count'] = $product_item['size1_count'];
                $params['size2_count'] = $product_item['size2_count'];
                $params['size3_count'] = $product_item['size3_count'];
                $params['size4_count'] = $product_item['size4_count'];
                $result[] = $this->product_warehouse_interface->editProductWarehouse($product_warehouse_primary, $params);
            } else {
                // درج خودکار درخواست کالا از انباری که موجودی مورد نیاز را دارد.
                if (!$result_check_stock['result']) {
                    $params = [
                        'company_id' => $company_id,
                        'product_id' => $product_warehouse_primary->product_id
                    ];
                    $select = ['id', 'product_id', 'free_size_count', 'size1_count', 'size2_count', 'size3_count', 'size4_count'];
                    // انبار کالا به ازای موجود بودن تمام درخواست های کالا
                    $product_warehouse = $this->product_warehouse_interface->getByStockProduct($params, $product_item, $select);
                    if (is_null($product_warehouse)) {
                        DB::rollBack();
                        return [
                            'result' => false,
                            'message' => $result_check_stock['message'],
                            'data' => $result_check_stock['data']
                        ];
                    }

                    // محاسبه برای درخواست کسری موجودی برای انبار اول
                    $params = null;
                    $params['warehouse_id'] = $product_warehouse_primary->id;
                    $params['free_size_count'] = 0;
                    $params['size1_count'] = 0;
                    $params['size2_count'] = 0;
                    $params['size3_count'] = 0;
                    $params['size4_count'] = 0;
                    if (in_array('free_size_count', $result_check_stock['size'])) {
                        $params['free_size_count'] = $product_warehouse->free_size_count - $product_item['free_size_count'];
                    }
                    if (in_array('size1_count', $result_check_stock['size'])) {
                        $params['size1_count'] = $product_warehouse->size1_count - $product_item['size1_count'];
                    }
                    if (in_array('size2_count', $result_check_stock['size'])) {
                        $params['size2_count'] = $product_warehouse->size2_count - $product_item['size2_count'];
                    }
                    if (in_array('size3_count', $result_check_stock['size'])) {
                        $params['size3_count'] = $product_warehouse->size3_count - $product_item['size3_count'];
                    }
                    if (in_array('size4_count', $result_check_stock['size'])) {
                        $params['size4_count'] = $product_warehouse->size4_count - $product_item['size4_count'];
                    }
                    // درج درخواست کسری موجودی برای سفارش جاری
                    $result[] = $this->request_product_interface->addRequestProductWarehouse($params, $user)['result'];

                    $params = null;
                    // کسر موجودی فروخته شده از انبار اتومات
                    $params['sign'] = 'minus';
                    $params['free_size_count'] = $product_item['free_size_count'];
                    $params['size1_count'] = $product_item['size1_count'];
                    $params['size2_count'] = $product_item['size2_count'];
                    $params['size3_count'] = $product_item['size3_count'];
                    $params['size4_count'] = $product_item['size4_count'];
                    $result[] = $this->product_warehouse_interface->editProductWarehouse($product_warehouse, $params);
                }
            }
        }

        foreach ($inputs['payments'] as $payment_item) {
            $res = $this->factor_payment_interface->addFactorPayment($payment_item, $res_factor['data']['id'], $user);
            $result[] = $res['result'];
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
            'data' => $flag ? $res_factor['data']['code'] : null
        ];
    }

    /**
     * حذف فاکتور
     * @param $code
     * @return array
     */
    public function deleteFactor($code): array
    {
        $factor = $this->factor_interface->getFactorByCode($code, ['id']);
        if (is_null($factor)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        DB::beginTransaction();
        $result[] = $this->factor_interface->deleteFactor($factor);
        $result[] = (bool)$this->factor_product_interface->deleteFactorProduct($factor->id);
        $result[] = (bool)$this->factor_payment_interface->deleteFactorPayment($factor->id);

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
