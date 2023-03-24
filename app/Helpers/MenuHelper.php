<?php

namespace App\Helpers;

use App\Exceptions\ApiException;
use App\Facades\ProductWarehouseFacade;
use App\Repositories\Interfaces\iCustomer;
use App\Repositories\Interfaces\iFactor;
use App\Repositories\Interfaces\iFactorPayment;
use App\Repositories\Interfaces\iFactorProduct;
use App\Repositories\Interfaces\iMenu;
use App\Repositories\Interfaces\iProductWarehouse;
use App\Repositories\Interfaces\iRequestProductWarehouse;
use App\Traits\Common;
use App\Traits\FactorTrait;
use App\Traits\RequestProductWarehouseTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MenuHelper
{
    use Common;

    // attributes
    public iMenu $menu_interface;

    public function __construct(iMenu $menu_interface)
    {
        $this->menu_interface = $menu_interface;
    }

    private function checkMenuPermission($menu, $user)
    {
        $permissions = $user->getAllPermissions()->pluck('name')->toArray();
        foreach ($menu as $key => $menu_item) {
            if ($menu_item->has_permission) {
                if (
                    !in_array("admin-$menu_item->route_name", $permissions) &&
                    !in_array("edit-$menu_item->route_name", $permissions) &&
                    !in_array("view-$menu_item->route_name", $permissions)
                ) {
                    unset($menu[$key]);
                }
            }
        }

        return $menu;
    }

    /**
     * منو
     * @return array
     */
    public function getMenu(): array
    {
        $user = Auth::user();
        $menu = $this->menu_interface->getMenu();
        $menu = $this->checkMenuPermission($menu, $user);
        $menu = $menu->toArray();
        $menu = $this->buildTree($menu, 0, ['parent_id', 'has_permission']);

        return [
            'result' => true,
            'message' => __('messages.success'),
            'data' => $menu
        ];
    }

}
