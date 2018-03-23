<?php

namespace App;

use Nette;
use Nette\Application\Routers\RouteList;
use Nette\Application\Routers\Route;

class RouterFactory {

    use Nette\StaticClass;

    /**
     * @return Nette\Application\IRouter
     */
    public static function createRouter() {
        $router = new RouteList;
        $router[] = $module = new RouteList('Admin');
        $module[] = new Route('admin/<presenter>/<action>', 'Homepage:default');

        $router[] = $module = new RouteList('Front');
        $module[] = new Route('index.php', 'Front:Default:default', Route::ONE_WAY);
        $module[] = new Route('udrzba-hrist', 'Homepage:meintenanceField');
        $module[] = new Route('cesty-za-fotbalem', 'Homepage:travel');
        $module[] = new Route('ztracene-hriste', 'Homepage:lostField');
        $module[] = new Route('tipy-pro-trenery', 'Homepage:matches');
        $module[] = new Route('fotbalovy-glosar', 'Homepage:footballGlos');
        $module[] = new Route('spoluprace', 'Homepage:cooperation');
        $module[] = new Route('obchodni-podminky', 'Homepage:conditionals');
        $module[] = new Route('o-nas', 'Homepage:about');
        $module[] = new Route('napis-nam', 'Homepage:contact');
        $module[] = new Route('prihlaseni', 'Sign:in');
        $module[] = new Route('vyhledavani[/<q>]', 'Search:default');
        $module[] = new Route('karta-hriste[/<pitch_card_id>]', 'Search:pitchCard');
        $module[] = new Route('prispevek[/<article_id>]', 'Article:default');
        $module[] = new Route('soutez-spravcu', 'Competition:default');
        $module[] = new Route('<presenter>/<action>', 'Homepage:default');
        return $router;
    }

}
