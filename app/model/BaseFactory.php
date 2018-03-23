<?php

namespace App\Model;

use Models\ModelException;

/**
 *
 * @author Filip Březina <filip.brezina11@gmail.com>
 */
class BaseFactory extends BaseModel {

  public function __construct(\Nette\Database\Context $database) {
    parent::__construct($database);
  }

  

}
