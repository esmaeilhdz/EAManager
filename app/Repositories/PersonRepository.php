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
            $company_id = $this->getCurrentCompanyOfUser($user);
            return Person::query()
                ->with([
                    'creator:id,person_id',
                    'creator.person:id,name,family'
                ])
                ->select([
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
                ->whereHas('person_company', function ($q) use ($company_id) {
                    $q->where('company_id', $company_id);
                })
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
                'identity' => $inputs['identity'],
                'score' => $inputs['score'],
                'passport_no' => $inputs['passport_no'] ?? null
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
            $person->identity = $inputs['identity'];
            $person->passport_no = $inputs['passport_no'] ?? null;
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
