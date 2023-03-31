<?php

namespace App\Helpers;

use App\Repositories\Interfaces\iPerson;
use App\Traits\Common;
use Illuminate\Support\Facades\Auth;

class PersonHelper
{
    use Common;

    // attributes
    public iPerson $person_interface;

    public function __construct(iPerson $person_interface)
    {
        $this->person_interface = $person_interface;
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
        $person = $this->person_interface->getPersonByCode($code);
        if (is_null($person)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        return [
            'result' => true,
            'message' => __('messages.success'),
            'data' => $person
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
        $user = Auth::user();
        $result = $this->person_interface->addPerson($inputs, $user);
        return [
            'result' => $result['result'],
            'message' => $result['result'] ? __('messages.success') : __('messages.fail'),
            'data' => $result['data']
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
