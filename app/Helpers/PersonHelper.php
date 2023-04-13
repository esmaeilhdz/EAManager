<?php

namespace App\Helpers;

use App\Repositories\Interfaces\iCompany;
use App\Repositories\Interfaces\iPerson;
use App\Repositories\Interfaces\iPersonCompany;
use App\Traits\Common;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PersonHelper
{
    use Common;

    // attributes
    public iPerson $person_interface;
    public iCompany $company_interface;
    public iPersonCompany $person_company_interface;

    public function __construct(
        iPerson $person_interface,
        iCompany $company_interface,
        iPersonCompany $person_company_interface,
    )
    {
        $this->person_interface = $person_interface;
        $this->company_interface = $company_interface;
        $this->person_company_interface = $person_company_interface;
    }

    /**
     * سرویس لیست افراد
     * @param $inputs
     * @return array
     */
    public function getPersons($inputs): array
    {
        $user = Auth::user();
        $search_data = $param_array = [];
        $search_data[] = $this->GWC($inputs['search_txt'] ?? '', 'string:name;family;national_code;concat_ws(" ",name,family);replace(concat_ws("",name,family)," ","")');
        $inputs['where']['search']['condition'] = $this->generateWhereCondition($search_data, $param_array);
        $inputs['where']['search']['params'] = $param_array;

        $inputs['order_by'] = $this->orderBy($inputs, 'people');
        $inputs['per_page'] = $this->calculatePerPage($inputs);

        $persons = $this->person_interface->getPersons($inputs, $user);

        $persons->transform(function ($item) {
            return [
                'code' => $item->code,
                'name' => $item->name,
                'family' => $item->family,
                'national_code' => $item->national_code,
                'score' => $item->score,
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
            'data' => $persons
        ];
    }

    /**
     * سرویس افراد برای کومبو
     * @param $inputs
     * @return array
     */
    public function getPersonsCombo($inputs): array
    {
        $user = Auth::user();
        $search_data = $param_array = [];
        $search_data[] = $this->GWC($inputs['search_txt'] ?? '', 'string:name;family;concat_ws(" ",name,family);replace(concat_ws("",name,family)," ","")');
        $inputs['where']['search']['condition'] = $this->generateWhereCondition($search_data, $param_array);
        $inputs['where']['search']['params'] = $param_array;

        $inputs['order_by'] = $this->orderBy($inputs, 'people');
        $persons = $this->person_interface->getPersonsCombo($inputs, $user);

        $persons->transform(function ($item) {
            return [
                'code' => $item->code,
                'full_name' => $item->name . ' ' . $item->family,
            ];
        });

        return [
            'result' => true,
            'message' => __('messages.success'),
            'data' => $persons
        ];
    }

    /**
     * سرویس جزئیات فرد
     * @param $code
     * @return array
     */
    public function getPersonDetail($code): array
    {
        $select = ['id', 'internal_code', 'name', 'family', 'father_name', 'national_code', 'identity', 'passport_no', 'score'];
        $relation = [
            'attachment' => function ($q) {
                $q->select(['model_type', 'model_id', 'path', 'file_name', 'ext'])
                    ->where('type', 'thumb')
                    ->where('attachment_type_id', 1);
            }
        ];
        $person = $this->person_interface->getPersonByCode($code, $select, $relation);
        if (is_null($person)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $result['internal_code'] = $person->internal_code;
        $result['name'] = $person->name;
        $result['family'] = $person->family;
        $result['father_name'] = $person->father_name;
        $result['national_code'] = $person->national_code;
        $result['identity'] = $person->identity;
        $result['passport_no'] = $person->passport_no;
        $result['score'] = $person->score;

        $attachment_path = null;
        if (count($person->attachment)) {
            $attachment_path = env('APP_URL') . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . $person->attachment[0]->path . DIRECTORY_SEPARATOR . $person->attachment[0]->file_name . '.' . $person->attachment[0]->ext;
        }
        $result['attachment']['path'] = $attachment_path;

        return [
            'result' => true,
            'message' => __('messages.success'),
            'data' => $result
        ];
    }

    /**
     * سرویس ویرایش فرد
     * @param $inputs
     * @return array
     */
    public function editPerson($inputs): array
    {
        $person = $this->person_interface->getPersonByCode($inputs['code']);
        if (is_null($person)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $result = $this->person_interface->editPerson($inputs);
        return [
            'result' => (bool) $result,
            'message' => $result ? __('messages.success') : __('messages.fail'),
            'data' => null
        ];
    }

    /**
     * سرویس افزودن فرد
     * @param $inputs
     * @return array
     */
    public function addPerson($inputs): array
    {
        $company = $this->company_interface->getCompanyByCode($inputs['company_code'], ['id']);
        if (is_null($company)) {
            return [
                'result' => false,
                'message' => __('messages.company_not_found'),
                'data' => null
            ];
        }
        $inputs['company_id'] = $company->id;

        DB::beginTransaction();
        $user = Auth::user();
        $add_person_result = $this->person_interface->addPerson($inputs, $user);
        $result[] = $add_person_result['result'];
        $inputs['person_id'] = $add_person_result['data']->id;

        $result[] = $this->person_company_interface->addPersonCompany($inputs, $user)['result'];

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
            'data' => $add_person_result['data']->code
        ];
    }

    /**
     * سرویس حذف فرد
     * @param $inputs
     * @return array
     */
    public function deletePerson($inputs): array
    {
        $person = $this->person_interface->getPersonByCode($inputs['code'], ['id']);
        if (is_null($person)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $result = $this->person_interface->deletePerson($person->id);
        return [
            'result' => (bool) $result,
            'message' => $result ? __('messages.success') : __('messages.fail'),
            'data' => null
        ];
    }


}
