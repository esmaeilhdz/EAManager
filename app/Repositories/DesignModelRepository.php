<?php

namespace App\Repositories;

use App\Exceptions\ApiException;
use App\Models\DesignModel;
use App\Traits\Common;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class DesignModelRepository implements Interfaces\iDesignModel
{
    use Common;

    /**
     * لیست طراحی مدل ها
     * @param $inputs
     * @param $user
     * @return mixed
     * @throws ApiException
     */
    public function getDesignModels($inputs, $user): mixed
    {
        try {
            $company_id = $this->getCurrentCompanyOfUser($user);
            return DesignModel::with([
                'creator:id,person_id',
                'creator.person:id,name,family',
            ])
            ->select([
                'id',
                'name',
                'is_confirm',
                'created_by',
                'created_at'
            ])
            ->where('company_id', $company_id)
            ->whereRaw($inputs['where']['search']['condition'], $inputs['where']['search']['params'])
            ->paginate($inputs['per_page']);
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    /**
     * جزئیات طراحی مدل
     * @param $id
     * @param $user
     * @param array $select
     * @param array $relation
     * @return Builder|Builder[]|Collection|Model|null
     * @throws ApiException
     */
    public function getDesignModelById($id, $user, $select = [], $relation = []): Model|Collection|Builder|array|null
    {
        try {
            $company_id = $this->getCurrentCompanyOfUser($user);
            $design_model = DesignModel::where('id', $id)
                ->where('company_id', $company_id);

            if (count($relation)) {
                $design_model = $design_model->with($relation);
            }

            if (count($select)) {
                $design_model = $design_model->select($select);
            }

            return $design_model->first();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    /**
     * تایید طراحی مدل
     * @param $design_model
     * @param $inputs
     * @return mixed
     * @throws ApiException
     */
    public function confirmDesignModel($design_model, $inputs): mixed
    {
        try {
            $design_model->is_confirm = $inputs['is_confirm'];

            return $design_model->save();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    /**
     * ویرایش طراحی مدل
     * @param $design_model
     * @param $inputs
     * @return mixed
     * @throws ApiException
     */
    public function editDesignModel($design_model, $inputs): mixed
    {
        try {
            $design_model->name = $inputs['name'];
            $design_model->description = $inputs['description'];

            return $design_model->save();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    /**
     * درج طراحی مدل
     * @param $inputs
     * @param $user
     * @return array
     * @throws ApiException
     */
    public function addDesignModel($inputs, $user): array
    {
        try {
            $company_id = $this->getCurrentCompanyOfUser($user);
            $design_model = new DesignModel();

            $design_model->company_id = $company_id;
            $design_model->name = $inputs['name'];
            $design_model->description = $inputs['description'];
            $design_model->created_by = $user->id;

            $result = $design_model->save();

            return [
                'result' => $result,
                'data' => $result ? $design_model->id : null
            ];

        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    /**
     * حذف طراحی مدل
     * @param $design_model
     * @return mixed
     * @throws ApiException
     */
    public function deleteDesignModel($design_model): mixed
    {
        try {
            return $design_model->delete();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }
}
