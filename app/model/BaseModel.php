<?php

namespace App\Model;

/**
 *
 * @author Filip Březina <filip.brezina11@gmail.com>
 */
class BaseModel extends \Nette\Object {

    public $prefixTable = '';
    public $public;
    public $data;
    public $id;
    public $table;
    public $primary;
    public $imagePath;
    public $imagePathReal;
    public $defaultImage;
    public $imageWidth;
    public $imageHeight;
    public $defaultLang = 'cs';
    public $tags;

    /** @var Nette\Database\Context @inject */
    public $db;

    public function __construct(\Nette\Database\Context $database) {
        $this->db = $database;
        $this->primary = $this->getPrimaryName();
    }
    
    public function initID($id) {
    $this->id = $id;
    $this->load();
    return $this->data ? $this : FALSE;
  }

  public function initData($data) {
    $this->data = $data;
    $primary = $this->primary;
    if ($data instanceof Nette\Database\Row) {
      $data = get_object_vars($data);
    } else {
      $data = $data->toArray();
    }
    $this->id = $data[$primary];
    return $this;
  }

  public function getData() {
    return $this->data;
  }

  public function getID() {
    return $this->id;
  }

  public function load() {
    $this->data = $this->getTable()->select('*')->where($this->primary . ' = ?', $this->getID())->fetch();
  }

  public function get($col) {
    if (isset($this->data->$col)) {
      return $this->data->$col;
    } else {
      return NULL;
    }
  }

  public function set($col, $value) {
    try {
      $this->getTable()->where($this->primary . ' = ?', $this->getID())->update(array($col => $value));
      $this->load();
    } catch (PDOException $e) {
      throw new ModelException($e);
    }
  }

  /**
   * @return Nette\Database\Table\Selection
   */
  public function getTable($name = NULL) {
    if ($name) {
      return $this->db->table($name);
    }
    return $this->db->table($this->getTableName());
  }

  /**
   * Get name of object table
   * @return string
   */
  public function getTableName() {
    return $this->table;
  }

  /**
   * 
   * @param type $array
   * @return this
   */
  public function arrayToObject($array) {
    $objects = array();
    foreach ($array as $a) {
      $container = new \Nette\DI\Container;
      $instance = $container->createInstance(lcfirst(get_class($this)), array($this->db));
      array_push($objects, $instance->initData($a));
    }
    return $objects;
  }
  
   /**
  *
  * @param DibiRow $row
  * @return this
  */
 public function rowToObject(Nette\Database\Table\ActiveRow $row) {
   $container = new \Nette\DI\Container;
   $instance = $container->createInstance(lcfirst(get_class($this)), array($this->db));
   return $instance->initData($row);
 }
  
  /**
  * Returns instance of class
  *
  * @param string $name name of the class
  * @return Object
  */
 protected function createInstance($name) {
   $container = new \Nette\DI\Container;
   $instance = $container->createInstance(sprintf('App\Model\%s', ucfirst($name)), array($this->db));
   return $instance;
 }

  /**
   * Get primary name of table
   */
  private function getPrimaryName() {
    return 'id_' . $this->table;
  }

  public function delete() {
    $this->getTable()
            ->where($this->primary . ' = ?', $this->getID())
            ->delete();
  }

  public function update($data) {
    $this->getTable()
            ->where($this->primary . ' = ?', $this->getID())
            ->update($data);
  }

  public function insert($data) {
    $row = $this->getTable()
            ->insert($data);
    return $row->getPrimary();
  }

  /**
   * @return \Nette\Database\Table\Selection
   */
  public function getAll() {
    return $this->getTable()->select('*');
  }
  
}
