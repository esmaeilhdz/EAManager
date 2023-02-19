<?php

namespace App\Helpers;

use App\Repositories\Interfaces\iAccount;
use App\Traits\Common;
use Illuminate\Support\Facades\Auth;

class AccountHelper
{
    use Common;

    // attributes
    public iAccount $account_interface;

    public function __construct(iAccount $account_interface)
    {
        $this->account_interface = $account_interface;
    }

    /**
     * لیست حساب ها
     * @param $inputs
     * @return array
     */
    public function getAccounts($inputs): array
    {
        $search_data = $param_array = [];
        $search_data[] = $this->GWC($inputs['search_txt'] ?? '', 'string:branch_name');
        $inputs['where']['search']['condition'] = $this->generateWhereCondition($search_data, $param_array);
        $inputs['where']['search']['params'] = $param_array;

        $inputs['per_page'] = $this->calculatePerPage($inputs);

        $accounts = $this->account_interface->getAccounts($inputs);

        $accounts->transform(function ($item) {
            return [
                'code' => $item->code,
                'branch_name' => $item->branch_name,
                'account_no' => $item->account_no,
                'sheba_no' => $item->sheba_no,
                'card_no' => $item->card_no,
                'cheque_count' => count($item->account_cheques),
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
            'data' => $accounts
        ];
    }

    /**
     * جزئیات حساب
     * @param $code
     * @return array
     */
    public function getAccountDetail($code): array
    {
        $account = $this->account_interface->getAccountByCode($code);
        if (is_null($account)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        return [
            'result' => true,
            'message' => __('messages.success'),
            'data' => $account
        ];
    }

    /**
     * ویرایش حساب
     * @param $inputs
     * @return array
     */
    public function editAccount($inputs): array
    {
        $account = $this->account_interface->getAccountByCode($inputs['code']);
        if (is_null($account)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $result = $this->account_interface->editAccount($inputs);
        return [
            'result' => (bool) $result,
            'message' => $result ? __('messages.success') : __('messages.fail'),
            'data' => null
        ];
    }

    /**
     * افزودن حساب
     * @param $inputs
     * @return array
     */
    public function addAccount($inputs): array
    {
        $user = Auth::user();
        $result = $this->account_interface->addAccount($inputs, $user);
        return [
            'result' => $result['result'],
            'message' => $result['result'] ? __('messages.success') : __('messages.fail'),
            'data' => $result['data']
        ];
    }

    /**
     * حذف حساب
     * @param $id
     * @return array
     */
    public function deleteAccount($code): array
    {
        $account = $this->account_interface->getAccountByCode($code);
        if (is_null($account)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $result = $this->account_interface->deleteAccount($code);
        return [
            'result' => (bool) $result,
            'message' => $result ? __('messages.success') : __('messages.fail'),
            'data' => null
        ];
    }

}
