<?php

use Models\ModelException;

namespace App\Model;

/**
 *
 * @author Filip Březina <filip.brezina11@gmail.com>
 */
class Article extends BaseFactory {

    public function __construct(\Nette\Database\Context $database) {
        $this->table = 'article';
        parent::__construct($database);
    }

    public function getNameWriter($user_id) {
        return $this->db->table('user')
                        ->where('id_user', $user_id)
                        ->fetch();
    }

    public function add($values, $values_image) {
        unset($values['image']);
        $ins = $this->db->table($this->table)->insert($values);

        unset($values_image['name']);
        unset($values_image['content']);
        unset($values_image['short_content']);
        unset($values_image['category']);
        
        if ($values_image['image']) {
            foreach ($values_image['image'] as $image) {
                $this->db->table('image_article')->insert(array(
                    'name' => $image[0]->name,
                    'url' => 'uploads/article/800_' . $image[0]->name,
                    'thumb' => 'uploads/article/thumb_' . $image[0]->name,
                    'article_id' => $ins->id_article
                ));
            }
        } else {
            $this->db->table('image_article')->insert(array(
                'name' => 'pitch',
                'url' => 'uploads/article/800_football-pitch.jpg' ,
                'thumb' => 'uploads/article/thumb_football-pitch.jpg',
                'article_id' => $ins->id_article
            ));
        }
        
        return array('article_id' => $ins->id_article, 'name' => $ins->name);
    }

    public function update($values) {
        unset($values['image']);
        $this->db->table($this->table)->where('id_article', $this->getID())->update($values);
    }

    public function deleteArticle($article_id) {
        $this->db->table($this->table)
                ->where('id_article', $article_id)
                ->fetch()
                ->delete();
    }

    public function getArticleImages() {
        return $this->db->table('image_article')
                        ->where('article_id', $this->getID())
                        ->order('created DESC')
                        ->fetchAll();
    }

    public function getImage($article_id) {
        return $this->db->table('image_article')
                        ->where('article_id', $article_id)
                        ->order('created DESC')
                        ->fetchField('url');
    }

}
