<?php

namespace App\Repositories;

use App\Exceptions\ApiException;
use App\Models\Account;
use App\Traits\Common;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class AccountRepository implements Interfaces\iAccount
{
    use Common;

    /**
     * لیست حساب ها
     * @param $inputs
     * @return LengthAwarePaginator
     * @throws ApiException
     */
    public function getAccounts($inputs): LengthAwarePaginator
    {
        try {
            return Account::with([
                'creator:id,person_id',
                'creator.person:id,name,family',
                'account_cheques:account_id' => function ($q) {
                    $q->where('is_enable', 1);
                }
            ])
                ->select([
                    'id',
                    'code',
                    'branch_name',
                    'account_no',
                    'sheba_no',
                    'card_no',
                    'created_by',
                    'created_at'
                ])
                ->whereRaw($inputs['where']['search']['condition'], $inputs['where']['search']['params'])
                ->paginate($inputs['per_page']);
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    public function getAccountByCode($code)
    {
        try {
            return Account::with([
                'account_cheques:account_id,cheque_no_from,cheque_no_to,is_enable'
            ])
                ->select([
                    'id',
                    'code',
                    'branch_name',
                    'account_no',
                    'sheba_no',
                    'card_no'
                ])
                ->whereCode($code)
                ->first();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    public function editAccount($inputs)
    {
        try {
            return Account::whereCode($inputs['code'])
                ->update([
                    'branch_name' => $inputs['branch_name'],
                    'account_no' => $inputs['account_no'],
                    'sheba_no' => $inputs['sheba_no'],
                    'card_no' => $inputs['card_no']
                ]);
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    public function addAccount($inputs, $user)
    {
        try {
            $account = new Account();

            $account->code = $this->randomString();
            $account->branch_name = $inputs['branch_name'];
            $account->account_no = $inputs['account_no'];
            $account->sheba_no = $inputs['sheba_no'];
            $account->card_no = $inputs['card_no'];
            $account->created_by = $user->id;

            $result = $account->save();

            return [
                'result' => $result,
                'data' => $result ? $account->code : null
            ];
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    public function deleteAccount($code)
    {
        try {
            return Account::whereCode($code)->delete();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }
}
