<?php

namespace App\Traits;

use App\Models\RoleModel;
use Illuminate\Support\Facades\Auth;

trait RoleTrait
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * این تابع آرایه یک سطحی ای را که دارای ساختار پدر و فرزندی است تبدیل به یک آرایه درختی چند سطحی می کند.
     * @param array $elements
     * @param int $parent_id
     * @param array $unset_fields
     * @return array
     */
    private function buildTree(array $elements, int $parent_id = 0, array $unset_fields = []): array
    {
        $branch = [];

        foreach ($elements as $element) {
            if ($element['parent_id'] == $parent_id) {
                $children = $this->buildTree($elements, $element['id'], $unset_fields);

                if ($children) {
                    $element['children'] = $children;
                }

                if (count($unset_fields)) {
                    foreach ($unset_fields as $unset_field) {
                        unset($element[$unset_field]);
                    }
                }

                $branch[$element['id']] = $element;
            }
        }

        return array_values($branch);
    }

    /**
     * این تابع یک node را درون درخت پیدا می کند و آن node و فرزندان آن را برمی گرداند
     * @param array $elements
     * @param $find_id
     * @param $resource
     * @return mixed
     */
    private function findInTree(array $elements, $find_id, &$resource): mixed
    {

        foreach ($elements as $element) {
            if ($element['id'] == $find_id) {
                if (isset($element['children'])) {
                    $resource = $element['children'];
                }

                return $resource;
            }

            if (isset($element['children'])) {
                $this->find_in_tree($element['children'], $find_id, $resource);
            }
        }

        return $resource;
    }

    /**
     * این تابع تمام node های یک درخت را به یک آرایه یک سطحی تبدیل می کند
     * @param array $elements
     * @param $resource
     * @return mixed
     */
    private function convertTreeToArray(array $elements, &$resource): mixed
    {

        foreach ($elements as $element) {
            $resource[] = $element['id'];

            if (isset($element['children'])) {
                $this->convertTreeToArray($element['children'], $resource);
            }
        }

        return $resource;
    }

    /**
     * @param $element
     * @param $parent_depth
     * @param int $max_depth
     * @return mixed
     * @Comment Help : این تابع کل درخت را می گردد عمق هر نود را ثبت می کند و در نهایت بیشترین عمق درخت را بر می گرداند
     */
    private function makeTreeDepth(&$element, $parent_depth, int $max_depth = 0): mixed
    {
        $element['depth'] = $parent_depth + 1;
        $max_depth = ($element['depth'] > $max_depth) ? $element['depth'] : $max_depth;
        if (isset($element['children'])) {
            foreach ($element['children'] as &$child) {
                $max_depth_child = $this->make_tree_depth($child, $element['depth'], $max_depth);
                $max_depth = ($max_depth_child > $max_depth) ? $max_depth_child : $max_depth;
            }
        }
        return $max_depth;
    }

    public function getRolesByUser($user)
    {
        $user_role_name = $user->getRoleNames()[0];
        $user_role = RoleModel::select(['id', 'parent_id'])->whereName($user_role_name)->withoutGlobalScopes()->first();
        $roles_array = RoleModel::query()->select(['id', 'parent_id'])->withoutGlobalScopes()->get()->all();

        $roles_array = $this->buildTree($roles_array, $user_role->parent_id);
        $this->findInTree($roles_array, $user_role->id, $resource);
        $role_ids = $this->convertTreeToArray($resource, $resource2);

        return array_merge([$user_role->id], $role_ids);
    }
}
