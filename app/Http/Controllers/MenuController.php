<?php


namespace App\Http\Controllers;

use App\Models\MenuItem;
use Illuminate\Routing\Controller as BaseController;

class MenuController extends BaseController
{

    public function getMenuItems()
    {
        $menuItems = MenuItem::all();

        $menu = [];
        foreach ($menuItems as $menuItem) {
            if (is_null($menuItem->parent_id)) {
                $menu[] = [
                    'id' => $menuItem->id,
                    'name' => $menuItem->name,
                    'url' => $menuItem->url,
                    'parent_id' => $menuItem->parent_id,
                    'created_at' => $menuItem->created_at,
                    'updated_at' => $menuItem->updated_at,
                    'children' => []
                ];
            }
        }

        foreach ($menu as &$item) {
            $item['children'] = $this->getChildren($item['id'], $menuItems);
        }

        return $menu;
    }

    private function getChildren($parentId, $menuItems)
    {
        $children = [];
        foreach ($menuItems as $menuItem) {
            if ($menuItem->parent_id === $parentId) {
                $children[] = [
                    'id' => $menuItem->id,
                    'name' => $menuItem->name,
                    'url' => $menuItem->url,
                    'parent_id' => $menuItem->parent_id,
                    'created_at' => $menuItem->created_at,
                    'updated_at' => $menuItem->updated_at,
                    'children' => $this->getChildren($menuItem->id, $menuItems)
                ];
            }
        }

        return $children;
    }
}
