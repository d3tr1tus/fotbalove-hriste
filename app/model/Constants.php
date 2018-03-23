<?php

/**
 *
 * @author Filip Březina <filip.brezina11@gmail.com>
 */
class Constants {

    const ROLE_ADMIN = 'super_admin',
            MEMBER = 'member';

    public static $regions = array(
        1 => 'Hlavní město Praha',
        'Středočeský kraj',
        'Jihočeský kraj',
        'Plzeňský kraj',
        'Karlovarský kraj',
        'Ústecký kraj',
        'Liberecký kraj',
        'Královéhradecký kraj',
        'Pardubický kraj',
        'Kraj Vysočina',
        'Jihomoravský kraj',
        'Olomoucký kraj',
        'Moravskoslezský kraj',
        'Zlínský kraj'
    );
    
    const FOOD = 'Občerstvení',
            PUB = 'Hospoda',
            PARKING = 'Parkování',
            TRIBUNE = 'Tribuna',
            GRASS = 'Tráva',
            FAKE_GRASS_TRAIN = 'Umělá tráva - tréninková 65x35 m',
            PLAYGROUND = 'Dětské hřiště';
    
    public static $we_have = array(
        'pub' => self::PUB,
        'food' => self::FOOD,
        'parking' => self::PARKING,
        'tribune' => self::TRIBUNE,
        'grass' => self::GRASS,
        'fake_grass_train' => self::FAKE_GRASS_TRAIN,
        'playground' => self::PLAYGROUND,
    );
    
    public static $team_kategory = array(
        'Muži' => 'Muži',
        'Juniorka' => 'Juniorka',
        'Dorost' => 'Dorost',
        'Žáci' => 'Žáci',
        'Přípravka' => 'Přípravka',
        'Školička' => 'Školička'
    );
    
    public static $article_category = array(
        'repair_pitch' => 'Údržba hřiště',
        'lost_pitch' => 'Ztracené hřiště',
        'travel' => 'Cesty za fotbalem',
        'tips' => 'Tipy pro trenéry',
        'football-glos' => 'Fotbalový glosář'
    );

}
