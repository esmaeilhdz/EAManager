<?php

namespace App\Repositories;

use App\Exceptions\ApiException;
use App\Models\Enumeration;
use App\Traits\Common;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EnumerationRepository implements Interfaces\iEnumeration
{
    use Common;

    /**
     * لیست مقادیر ها
     * @param $inputs
     * @return LengthAwarePaginator
     * @throws ApiException
     */
    public function getEnumerations($inputs): LengthAwarePaginator
    {
        try {
            return Enumeration::with([
                'creator:id,person_id',
                'creator.person:id,name,family'
            ])
                ->select([
                    'category_name',
                    'category_caption',
                    'is_enable',
                    'is_editable',
                    'created_by'
                ])
                ->distinct()
                ->whereRaw($inputs['where']['search']['condition'], $inputs['where']['search']['params'])
                ->orderByDesc('id')
                ->paginate($inputs['per_page']);
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    /**
     * گروه مقادیر
     * @param $category_name
     * @param array $select
     * @return mixed
     * @throws ApiException
     */
    public function getEnumerationByCategory($category_name, array $select = []): mixed
    {
        try {
            $enumeration = Enumeration::where('category_name', $category_name);

            if (count($select)) {
                $enumeration = $enumeration->select($select);
            }

            return $enumeration->get();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    /**
     * جزئیات مقادیر
     * @param $inputs
     * @param array $select
     * @return mixed
     * @throws ApiException
     */
    public function getEnumerationDetail($inputs, array $select = []): mixed
    {
        try {
            $enumeration = Enumeration::where('category_name', $inputs['category_name']);

            if (isset($inputs['enum_id'])) {
                $enumeration = $enumeration->where('enum_id', $inputs['enum_id']);
            }

            if (count($select)) {
                $enumeration = $enumeration->select($select);
            }

            return $enumeration->first();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    public function getEnumerationById($id, array $select = [])
    {
        try {
            $enumeration = Enumeration::where('id', $id);

            if (count($select)) {
                $enumeration = $enumeration->select($select);
            }

            return $enumeration->first();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    public function getEnumerationAll()
    {
        try {
            return Enumeration::select([
                'category_name',
                'category_caption',
                'enum_caption',
                'enum_id'
            ])
                ->where('is_enable', 1)
                ->get()
                ->groupBy('category_name');
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    /**
     * ویرایش مقادیر
     * @param $enumeration
     * @param $inputs
     * @return mixed
     * @throws ApiException
     */
    public function editEnumeration($enumeration, $inputs): mixed
    {
        try {
            $enumeration->enum_caption = $inputs['enum_caption'];

            return $enumeration->save();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    /**
     * افزودن مقادیر
     * @param $enumeration
     * @param $inputs
     * @param $user
     * @return bool
     * @throws ApiException
     */
    public function addEnumeration($enumeration, $inputs, $user): bool
    {
        try {
            $new_enumeration = new Enumeration();

            $new_enumeration->category_name = $enumeration->category_name;
            $new_enumeration->category_caption = $enumeration->category_caption;
            $new_enumeration->enum_caption = $inputs['enum_caption'];
            $new_enumeration->enum_id = Enumeration::select('enum_id')->where('category_name', $enumeration->category_name)->latest('id')->first()->enum_id + 1;
            $new_enumeration->is_enable = $enumeration->is_enable;
            $new_enumeration->is_editable = 1;
            $new_enumeration->created_by = $user->id;

            return $new_enumeration->save();

        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    /**
     * حذف مقادیر
     * @param $enumeration
     * @return mixed
     * @throws ApiException
     */
    public function deleteEnumeration($enumeration): mixed
    {
        try {
            return $enumeration->delete();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }
}
