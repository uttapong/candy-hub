<?php

namespace GetCandy\Cms\Services\Navigation;

use Event;

class NavigationService
{
    public static function render($menuClass)
    {
        if (class_exists($menuClass)) {
            $r = new \ReflectionClass($menuClass);
        } else {
            $r = new \ReflectionClass('GetCandy\\Cms\\Services\\Navigation\\Menus\\' . $menuClass);
        }

        $menu = $r->newInstanceArgs([]);

        Event::fire('cms.navigation.pre_render', [$menu]);

        return $menu->render();
    }
}