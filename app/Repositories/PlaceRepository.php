<?php

namespace App\Repositories;

use App\Exceptions\ApiException;
use App\Models\Place;
use App\Traits\Common;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class PlaceRepository implements Interfaces\iPlace
{
    use Common;

    /**
     * لیست مکان ها
     * @param $inputs
     * @return mixed
     * @throws ApiException
     */
    public function getPlaces($inputs): mixed
    {
        try {
            return Place::with([
                'creator:id,person_id',
                'creator.person:id,name,family',
                'place_kind:enum_id,enum_caption',
                'place_attribute' => function ($q) {
                    $q->select(['place_id' ,'place_attribute_id', 'value'])->where('place_attribute_id', 1);
                },
                'place_attribute.attribute:enum_id,enum_caption'
            ])
            ->select([
                'id',
                'name',
                'place_kind_id',
                'department_manager_name',
                'department_manager_national_code',
                'department_manager_identity',
                'capacity',
                'from_date',
                'created_by',
                'created_at'
            ])
            ->whereRaw($inputs['where']['search']['condition'], $inputs['where']['search']['params'])
            ->paginate($inputs['per_page']);
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    /**
     * @param $id
     * @return Builder|Builder[]|Collection|Model|null
     * @throws ApiException
     */
    public function getPlaceById($id): Model|Collection|Builder|array|null
    {
        try {
            return Place::with([
                'place_kind:enum_id,enum_caption',
                'place_attribute:place_id,place_attribute_id,value',
                'place_attribute.attribute:enum_id,enum_caption'
            ])
                ->select([
                    'id',
                    'name',
                    'place_kind_id',
                    'department_manager_name',
                    'department_manager_national_code',
                    'department_manager_identity',
                    'capacity',
                    'from_date'
                ])
                ->find($id);
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    public function getPlaceCombo($inputs, $user)
    {
        try {
            $company_id = $this->getCurrentCompanyOfUser($user);
            return Place::select([
                'id',
                'name',
            ])
                ->where('name', 'like', '%'.$inputs['search_txt'].'%')
                ->where('company_id', $company_id)
                ->limit(50)
                ->get();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    public function editPlace($inputs)
    {
        try {
            $update = [
                'name' => $inputs['name'],
                'place_kind_id' => $inputs['place_kind_id'],
                'department_manager_name' => $inputs['department_manager_name'],
                'department_manager_national_code' => $inputs['department_manager_national_code'] ?? null,
                'department_manager_identity' => $inputs['department_manager_identity'] ?? null,
                'capacity' => $inputs['capacity'],
                'from_date' => $inputs['from_date']
            ];

            if (empty($inputs['department_manager_national_code'])) {
                unset($update['department_manager_national_code']);
            }

            if (empty($inputs['department_manager_identity'])) {
                unset($update['department_manager_identity']);
            }

            return Place::where('id', $inputs['id'])->update($update);
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    public function addPlace($inputs, $user): array
    {
        try {
            $company_id = $this->getCurrentCompanyOfUser($user);
            $place = new Place();

            $place->name = $inputs['name'];
            $place->company_id = $company_id;
            $place->place_kind_id = $inputs['place_kind_id'];
            $place->department_manager_name = $inputs['department_manager_name'];
            $place->department_manager_national_code = $inputs['department_manager_national_code'] ?? null;
            $place->department_manager_identity = $inputs['department_manager_identity'] ?? null;
            $place->capacity = $inputs['capacity'];
            $place->from_date = $inputs['from_date'];
            $place->created_by = $user->id;

            $result = $place->save();

            return [
                'result' => $result,
                'data' => $result ? $place->id : null
            ];

        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    public function deletePlace($id)
    {
        try {
            return Place::where('id', $id)->delete();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }
}
