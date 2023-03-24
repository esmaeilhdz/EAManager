<?php

namespace App\Repositories;

use App\Exceptions\ApiException;
use App\Models\Factor;
use App\Models\Menu;
use App\Traits\Common;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class MenuRepository implements Interfaces\iMenu
{
    use Common;

    /**
     * لیست فاکتورها
     * @return Builder[]|Collection
     * @throws ApiException
     */
    public function getMenu(): Collection|array
    {
        try {
            return Menu::query()
                ->select([
                    'id',
                    'parent_id',
                    'caption',
                    'route_name',
                    'icon',
                    'has_permission'
                ])
                ->where('is_enable', 1)
                ->get();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

}
