<?php

namespace App\Helpers;

use App\Repositories\Interfaces\iCustomer;
use App\Repositories\Interfaces\iInvoice;
use App\Repositories\Interfaces\iInvoiceProduct;
use App\Traits\Common;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InvoiceHelper
{
    use Common;

    // attributes
    public iInvoice $invoice_interface;
    public iInvoiceProduct $invoice_product_interface;
    public iCustomer $customer_interface;

    public function __construct(
        iInvoice $invoice_interface,
        iCustomer $customer_interface,
        iInvoiceProduct $invoice_product_interface
    )
    {
        $this->invoice_interface = $invoice_interface;
        $this->customer_interface = $customer_interface;
        $this->invoice_product_interface = $invoice_product_interface;
    }

    /**
     * لیست پیش فاکتور ها
     * @param $inputs
     * @return array
     */
    public function getInvoices($inputs): array
    {
        $search_data = $param_array = [];
        $search_data[] = $this->GWC($inputs['search_txt'] ?? '', 'string:name;mobile');
        $inputs['where']['search']['condition'] = $this->generateWhereCondition($search_data, $param_array);
        $inputs['where']['search']['params'] = $param_array;

        $search_data = $param_array = [];
        $search_data[] = $this->GWC($inputs['search_txt'] ?? '', 'string:name;family;national_code;concat_ws(" ",name,family);replace(concat_ws("",name,family)," ","")');
        $inputs['where']['customer']['condition'] = $this->generateWhereCondition($search_data, $param_array);
        $inputs['where']['customer']['params'] = $param_array;

        $inputs['order_by'] = $this->orderBy($inputs, 'invoices');
        $inputs['per_page'] = $this->calculatePerPage($inputs);

        $invoices = $this->invoice_interface->getInvoices($inputs);

        $invoices->transform(function ($item) {
            return [
                'code' => $item->code,
                'customer' => is_null($item->customer_id) ? null : [
                    'name' => $item->customer->name,
                    'mobile' => $item->customer->mobile,
                ],
                'name' => $item->name,
                'mobile' => $item->mobile,
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
            'data' => $invoices
        ];
    }

    /**
     * جزئیات پیش فاکتور
     * @param $code
     * @return array
     */
    public function getInvoiceDetail($code): array
    {
        $select = ['id', 'code', 'customer_id', 'name', 'mobile', 'final_price'];
        $relation = [
            'customer:id,name,mobile,score',
            'invoice_products:invoice_id,product_warehouse_id,free_size_count,size1_count,size2_count,size3_count,size4_count',
            'invoice_products.product_warehouse:id,product_id',
            'invoice_products.product_warehouse.product:id,name',
        ];
        $invoice = $this->invoice_interface->getInvoiceByCode($code, $select, $relation);
        if (is_null($invoice)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $invoice_products = [];
        foreach ($invoice->invoice_products as $invoice_product) {
            $invoice_products[] = [
                'free_size_count' => $invoice_product->free_size_count,
                'size1_count' => $invoice_product->size1_count,
                'size2_count' => $invoice_product->size2_count,
                'size3_count' => $invoice_product->size3_count,
                'size4_count' => $invoice_product->size4_count,
                'product' => $invoice_product->product_warehouse->product->name
            ];
        }

        $invoice = $invoice->toArray();
        $invoice['invoice_products'] = $invoice_products;

        return [
            'result' => true,
            'message' => __('messages.success'),
            'data' => $invoice
        ];
    }

    /**
     * ویرایش پیش فاکتور
     * @param $inputs
     * @return array
     */
    public function editInvoice($inputs): array
    {
        $invoice = $this->invoice_interface->getInvoiceByCode($inputs['code'], ['id']);
        if (is_null($invoice)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        if (!empty($inputs['customer_code'])) {
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
        }

        DB::beginTransaction();
        $result[] = $this->invoice_interface->editInvoice($invoice, $inputs);
        foreach ($inputs['products'] as $item) {
            $invoice_product = $this->invoice_product_interface->getById($invoice->id, $item['id']);
            if (is_null($invoice_product)) {
                return [
                    'result' => false,
                    'message' => __('messages.invoice_product_not_found'),
                    'data' => null
                ];
            }
            $result[] = $this->invoice_product_interface->editInvoiceProduct($invoice_product, $item);
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
     * افزودن پیش فاکتور
     * @param $inputs
     * @return array
     */
    public function addInvoice($inputs): array
    {
        $user = Auth::user();
        if (!empty($inputs['customer_code'])) {
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
        }

        DB::beginTransaction();
        $res_invoice = $this->invoice_interface->addInvoice($inputs, $user);
        $result[] = $res_invoice['result'];
        foreach ($inputs['products'] as $item) {
            $res = $this->invoice_product_interface->addInvoiceProduct($item, $res_invoice['data']['id'], $user);
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
            'data' => $flag ? $res_invoice['data']['code'] : null
        ];
    }

    /**
     * حذف پیش فاکتور
     * @param $code
     * @return array
     */
    public function deleteInvoice($code): array
    {
        $invoice = $this->invoice_interface->getInvoiceByCode($code, ['id']);
        if (is_null($invoice)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        DB::beginTransaction();
        $result[] = $this->invoice_interface->deleteInvoice($invoice);
        $result[] = (bool) $this->invoice_product_interface->deleteInvoiceProduct($invoice->id);

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
