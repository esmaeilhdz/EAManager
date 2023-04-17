<?php

namespace App\Helpers;

use App\Repositories\Interfaces\iEnumeration;
use App\Traits\Common;
use Illuminate\Support\Facades\Auth;

class EnumerationHelper
{
    use Common;

    // attributes
    public iEnumeration $enumeration_interface;

    public function __construct(iEnumeration $enumeration_interface)
    {
        $this->enumeration_interface = $enumeration_interface;
    }

    private function checkEditable($enumeration): bool
    {
        $result = false;
        if ($enumeration->is_editable == 1) {
            $result = true;
        }

        return $result;
    }


    /**
     * لیست مقادیر ها
     * @param $inputs
     * @return array
     */
    public function getEnumerations($inputs): array
    {
        $search_data = $param_array = [];
        $search_data[] = $this->GWC($inputs['search_txt'] ?? '', 'string:category_caption');
        $inputs['where']['search']['condition'] = $this->generateWhereCondition($search_data, $param_array);
        $inputs['where']['search']['params'] = $param_array;

        $inputs['order_by'] = $this->orderBy($inputs, 'enumerations');
        $inputs['per_page'] = $this->calculatePerPage($inputs);

        $enumerations = $this->enumeration_interface->getEnumerations($inputs);

        $enumerations->transform(function ($item) {
            return [
                'category_name' => $item->category_name,
                'category_caption' => $item->category_caption,
                'is_enable' => $item->is_enable,
                'is_editable' => $item->is_editable,
                'creator' => is_null($item->creator->person) ? null : [
                    'person' => [
                        'full_name' => $item->creator->person->name . ' ' . $item->creator->person->family,
                    ]
                ],
            ];
        });

        return [
            'result' => true,
            'message' => __('messages.success'),
            'data' => $enumerations
        ];
    }

    /**
     * گروه مقادیر
     * @param $category_name
     * @return array
     */
    public function getEnumerationGrouped($category_name): array
    {
        $select = ['id', 'category_name', 'category_caption', 'enum_id', 'enum_caption'];
        $enumerations = $this->enumeration_interface->getEnumerationByCategory($category_name, $select);
        if (is_null($enumerations)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        return [
            'result' => true,
            'message' => __('messages.success'),
            'data' => $enumerations
        ];
    }

    /**
     * ویرایش مقادیر
     * @param $inputs
     * @return array
     */
    public function editEnumeration($inputs): array
    {
        $select = ['id', 'enum_caption', 'is_editable'];
        $enumeration = $this->enumeration_interface->getEnumerationDetail($inputs, $select);
        if (is_null($enumeration)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $result_check_editable = $this->checkEditable($enumeration);
        if (!$result_check_editable) {
            return [
                'result' => false,
                'message' => __('messages.cannot_edit_record'),
                'data' => null
            ];
        }

        $result = $this->enumeration_interface->editEnumeration($enumeration, $inputs);
        return [
            'result' => (bool) $result,
            'message' => $result ? __('messages.success') : __('messages.fail'),
            'data' => null
        ];
    }

    /**
     * افزودن مقادیر
     * @param $inputs
     * @return array
     */
    public function addEnumeration($inputs): array
    {
        $select = ['category_name', 'category_caption', 'enum_caption', 'is_enable', 'is_editable'];
        $enumeration = $this->enumeration_interface->getEnumerationDetail($inputs, $select);
        if (is_null($enumeration)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $result_check_editable = $this->checkEditable($enumeration);
        if (!$result_check_editable) {
            return [
                'result' => false,
                'message' => __('messages.cannot_edit_record'),
                'data' => null
            ];
        }

        $user = Auth::user();
        $result = $this->enumeration_interface->addEnumeration($enumeration, $inputs, $user);
        return [
            'result' => $result,
            'message' => $result ? __('messages.success') : __('messages.fail'),
            'data' => null
        ];
    }

    /**
     * حذف مقادیر
     * @param $code
     * @return array
     */
    public function deleteEnumeration($id): array
    {
        $enumeration = $this->enumeration_interface->getEnumerationById($id, ['id', 'is_editable']);
        if (is_null($enumeration)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $result_check_editable = $this->checkEditable($enumeration);
        if (!$result_check_editable) {
            return [
                'result' => false,
                'message' => __('messages.cannot_delete_record'),
                'data' => null
            ];
        }

        $result = $this->enumeration_interface->deleteEnumeration($enumeration);
        return [
            'result' => (bool) $result,
            'message' => $result ? __('messages.success') : __('messages.fail'),
            'data' => null
        ];
    }

}
