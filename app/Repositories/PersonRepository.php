<?php

namespace App\Repositories;

use App\Exceptions\ApiException;
use App\Models\Person;
use App\Traits\Common;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class PersonRepository implements Interfaces\iPerson
{
    use Common;

    /**
     * لیست افراد
     * @param $inputs
     * @param $user
     * @return LengthAwarePaginator
     * @throws ApiException
     */
    public function getPersons($inputs, $user): LengthAwarePaginator
    {
        try {
            $company_id = null;
            if (!$user->hasRole('super_admin')) {
                $company_id = $this->getCurrentCompanyOfUser($user);
            }
            return Person::query()
                ->with([
                    'person_company:person_id,company_id',
                    'person_company.company:id,code,name',
                    'creator:id,person_id',
                    'creator.person:id,name,family'
                ])
                ->select([
                    'id',
                    'code',
                    'internal_code',
                    'name',
                    'family',
                    'national_code',
                    'score',
                    'created_by',
                    'created_at'
                ])
                ->whereRaw($inputs['where']['search']['condition'], $inputs['where']['search']['params'])
                ->when(!is_null($company_id), function ($q) use ($company_id) {
                    $q->whereHas('person_company', function ($q2) use ($company_id) {
                        $q2->where('company_id', $company_id);
                    });
                })
                ->where('id', '<>', 1)
                ->orderByRaw($inputs['order_by'])
                ->paginate($inputs['per_page']);
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    public function getPersonsCombo($inputs, $user)
    {
        try {
            $company_id = $this->getCurrentCompanyOfUser($user);
            return Person::select([
                    'code',
                    'name',
                    'family'
                ])
                ->whereRaw($inputs['where']['search']['condition'], $inputs['where']['search']['params'])
                ->whereHas('person_company', function ($q) use ($company_id) {
                    $q->where('company_id', $company_id);
                })
                ->orderByRaw($inputs['order_by'])
                ->limit(20)
                ->get();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    /**
     * جزئیات فرد
     * @param $id
     * @param array $select
     * @param array $relation
     * @return mixed
     * @throws ApiException
     */
    public function getPersonById($id, $select = [], $relation = []): mixed
    {
        try {
            $person = Person::where('id', $id);

            if (count($select)) {
                $person = $person->select($select);
            }

            if (count($relation)) {
                $person = $person->with($relation);
            }

            return $person->first();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    /**
     * جزئیات فرد
     * @param $code
     * @param array $select
     * @param array $relation
     * @return mixed
     * @throws ApiException
     */
    public function getPersonByCode($code, $select = [], $relation = []): mixed
    {
        try {
            $person = Person::whereCode($code);

            if (count($select)) {
                $person = $person->select($select);
            }

            if (count($relation)) {
                $person = $person->with($relation);
            }

            return $person->first();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    /**
     * ویرایش فرد
     * @param $inputs
     * @return mixed
     * @throws ApiException
     */
    public function editPerson($inputs): mixed
    {
        try {
            $update = [
                'internal_code' => $inputs['internal_code'],
                'name' => $inputs['name'],
                'family' => $inputs['family'],
                'father_name' => $inputs['father_name'],
                'national_code' => $inputs['national_code'],
                'insurance_no' => $inputs['insurance_no'],
                'mobile' => $inputs['mobile'],
                'identity' => $inputs['identity'],
                'score' => $inputs['score'],
                'passport_no' => $inputs['passport_no'],
//                'card_no' => $inputs['card_no'],
//                'bank_account_no' => $inputs['bank_account_no'],
//                'sheba_no' => $inputs['sheba_no']
            ];

            if (empty($inputs['passport_no'])) {
                unset($update['passport_no']);
            }

            return Person::whereCode($inputs['code'])->update($update);
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    /**
     * افزودن فرد
     * @param $inputs
     * @param $user
     * @return array
     * @throws ApiException
     */
    public function addPerson($inputs, $user): array
    {
        try {
            $person = new Person();

            $person->code = $this->randomString();
            $person->internal_code = $this->randomPersonnelCode();
            $person->name = $inputs['name'];
            $person->family = $inputs['family'];
            $person->father_name = $inputs['father_name'];
            $person->national_code = $inputs['national_code'];
            $person->insurance_no = $inputs['insurance_no'];
            $person->mobile = $inputs['mobile'];
            $person->identity = $inputs['identity'];
            $person->passport_no = $inputs['passport_no'] ?? null;
//            $person->card_no = $inputs['card_no'];
//            $person->sheba_no = $inputs['sheba_no'];
//            $person->bank_account_no = $inputs['bank_account_no'];
            $person->created_by = $user->id;

            $result = $person->save();

            return [
                'result' => $result,
                'data' => $result ? $person : null
            ];

        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    /**
     * حذف فرد
     * @param $id
     * @return mixed
     * @throws ApiException
     */
    public function deletePerson($id): mixed
    {
        try {
            return Person::where('id', $id)->delete();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }
}
