<?php

use Models\ModelException;

namespace App\Model;

/**
 *
 * @author Filip Březina <filip.brezina11@gmail.com>
 */
class UserData extends BaseFactory {

    public function __construct(\Nette\Database\Context $database) {
        parent::__construct($database);
    }
    
    public function getFullName() {
        return $this->db->table('user')
                ->where('id_user', $this->getID())
                ->fetch();        
    }
    
    public function getActivatedAccount($user_id) {
        return $this->db->table('user')
                ->where('id_user', $user_id)
                ->fetch();
    }

}
