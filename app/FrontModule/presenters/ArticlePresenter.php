<?php

namespace App\FrontModule\Presenters;

use Nette;
use App\Model;

class ArticlePresenter extends BasePresenter {

    public function renderDefault($article_id) {
        $this->template->article = $this->article->initID($article_id);
        $shows = $this->article->get('show');
        $time = $this->article->get('created');
        $this->article->set('show', $shows + 1);
        $this->article->set('created', $time);
    }

}
