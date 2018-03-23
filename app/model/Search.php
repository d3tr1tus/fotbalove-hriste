<?php

use Models\ModelException;

namespace App\Model;

/**
 *
 * @author Filip Březina <filip.brezina11@gmail.com>
 */
class Search extends BaseFactory {

    public function __construct(\Nette\Database\Context $database) {
        parent::__construct($database);
    }

    public function getSearchPitches($q = NULL, $region = NULL) {
        $query = $this->db->table('pitch')
                ->select('*')
                ->where('accepted NOT', NULL)
                ->where('declined', NULL)
                ->order('name');

        if ($q) {
            $query->where('name LIKE ? OR info LIKE ? OR adress LIKE ? OR admin LIKE ?', '%' . $q . '%', '%' . $q . '%', '%' . $q . '%', '%' . $q . '%');
        }

        if ($region) {
            $query->where('region', array_search($region, \Constants::$regions));
        }

        return $this->createInstance('Pitch')->arrayToObject($query->fetchAll());
    }

    public function filterByRegion($region) {
        $query = $this->db->table('pitch')
                ->select('*')
                ->where('region', $region)
                ->order('name');

        return $this->createInstance('Pitch')->arrayToObject($query->fetchAll());
    }

    public function countTeamsInRegion($region) {
        return $this->db->table('pitch')
                        ->where('region', $region)
                        ->where('accepted NOT', NULL)
                        ->count();
    }

    public function getLastThreeTeams() {
        $q = $this->db->table('pitch')
                ->where('accepted')
                ->limit(3)
                ->order('accepted DESC')
                ->fetchAll();

        return $this->createInstance('Pitch')->arrayToObject($q);
    }

}
