<?php

namespace App\AdminModule\Presenters;

use Nette;
use App\Model;

class HomepagePresenter extends BasePresenter {

    public function startup() {

        parent::startup();
    }

    public function renderDefault() {
        
    }

    public function renderAddArticle($article_id = NULL) {
        if ($article_id != NULL) {
            $this->article->initID($article_id);
            \Tracy\Debugger::barDump($this->article->getArticleImages());
            $this->template->imagesArticle = $this->article->getArticleImages();
        }
    }

    public function handleEditPitch($pitch_id) {
        $this->pitch->initID($pitch_id);
        $this->redirect('AddPitch:default', $this->pitch->getID());
    }

    public function createComponentAddArticle() {
        $form = new \AdminForm();

        $form->addText('name', 'Název')
                ->setRequired('Zadejte název');

        $form->addTextArea('content', 'Obsah');

        $form->addTextArea('short_content', 'Popis')
                ->setRequired('Přidejte popis');

        $form->addSelect('category', 'Kategorie', \Constants::$article_category)
                ->setPrompt('Vyberte kategorii')
                ->setRequired('Vyberte kategorii');

        $form->addFileUpload("image")
                ->setParams([
                    'type' => 'article'
        ]);

        $form->addSubmit('send', 'Uložit');

        if ($this->getParameter('article_id')) {
            $this->article->initID($this->getParameter('article_id'));
            $form->setDefaults(array(
                'name' => $this->article->get('name'),
                'content' => $this->article->get('content'),
                'short_content' => $this->article->get('short_content'),
                'category' => $this->article->get('category'),
            ));
        }

        $form->addHidden('user_id', $this->user->getId());

        $form->onSuccess[] = [$this, 'addArticleSuccessed'];

        return $form;
    }

    public function addArticleSuccessed(\AdminForm $form) {
        $values = $form->getValues();
        $values_image = $form->getValues();
        try {
            if ($this->getParameter('article_id')) {
                $this->article->initID($this->getParameter('article_id'));
                $this->article->update($values);
                $this->flashMessage('Příspěvek úspěšně uložen.', 'success');
            } else {
                $ins_values = $this->article->add($values, $values_image);

                $params = array(
                    'article_id' => $ins_values['article_id'],
                    'article_name' => $ins_values['name']
                );

                $this->newsletter->sendEmail($params, 'newsletter');
                $this->flashMessage('Příspěvek úspěšně přidán.', 'success');
            }
            $this->redirect('Homepage:allArticles');
        } catch (\PDOException $e) {
            $this->flashMessage('Nepodařilo se připojit k databázi. Kontaktujte prosím správce.');
        }
    }

    public function handleEditArticle($article_id) {
        $this->article->initID($article_id);
        $this->redirect('Homepage:addArticle', $this->article->getID());
    }

    public function handleDeleteArticle($article_id) {
        $this->article->deleteArticle($article_id);
        $this->flashMessage('Příspěvek byl smazán', 'info');
        $this->redirect('this');
    }

    public function handleDeletePitch($pitch_id) {
        $this->pitch->deletePitch($pitch_id);
        $this->flashMessage('Hřiště bylo smazáno', 'info');
        $this->redirect('this');
    }

}
