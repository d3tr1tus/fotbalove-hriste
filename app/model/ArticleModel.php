<?php

namespace App\Model;

use Models\ModelException;

/**
 *
 * @author Filip Březina <filip.brezina11@gmail.com>
 */
class ArticleModel extends BaseModel {

    public function __construct(\Nette\Database\Context $database) {
        parent::__construct($database);
    }

    public function getArticlesPagi() {
        return $this->db->table('image_article')
                        ->select('url')
                        ->select('article.*')
                        ->order('created DESC');
    }

    public function getArticlesAdmin() {
        $result = $this->db->table('article')
                ->select('article.*')
                ->order('created DESC')
                ->fetchAll();

        return $this->createInstance('Article')->arrayToObject($result);
    }

    public function getArticlesPagiCategory($category) {
        return $this->db->table('image_article')
                        ->select('url')
                        ->select('article.*')
                        ->where('category', $category)
                        ->order('created DESC');
    }

}
