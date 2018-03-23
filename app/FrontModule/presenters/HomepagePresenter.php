<?php

namespace App\FrontModule\Presenters;

use Nette;
use App\Model;
use IPub\VisualPaginator\Components as VisualPaginator;

class HomepagePresenter extends BasePresenter {

    public function startup() {

        parent::startup();
    }

    public function renderDefault() {
        $articles = $this->articles->getArticlesPagi();
        $this->presenter->visualPaginatorFunction($articles);
    }

    /**
     * Create items paginator
     *
     * @return VisualPaginator\Control
     */
    protected function createComponentVisualPaginator() {
        // Init visual paginator
        $control = new VisualPaginator\Control;

        $control->setTemplateFile(__DIR__ . '/../../components/visualpaginator.latte');

        $control->disableAjax();

        return $control;
    }

    public function visualPaginatorFunction($articles) {
        // Get visual paginator components
        $visualPaginator = $this['visualPaginator'];
        // Get paginator form visual paginator
        $paginator = $visualPaginator->getPaginator();
        // Define items count per one page
        $paginator->itemsPerPage = 3;
        // Define total items in list
        $paginator->itemCount = count($articles);
        // Apply limits to list
        $articles->limit($paginator->itemsPerPage, $paginator->offset);
        $this->template->articles = $articles;
    }

    public function renderTravel() {
        $articles = $this->articles->getArticlesPagiCategory('travel');
        $this->presenter->visualPaginatorFunction($articles);
    }

    public function renderMeintenanceField() {
        $articles = $this->articles->getArticlesPagiCategory('repair_pitch');
        $this->presenter->visualPaginatorFunction($articles);
    }

    public function renderLostField() {
        $articles = $this->articles->getArticlesPagiCategory('lost_pitch');
        $this->presenter->visualPaginatorFunction($articles);
    }

    public function renderMatches() {
        $articles = $this->articles->getArticlesPagiCategory('tips');
        $this->presenter->visualPaginatorFunction($articles);
    }

    public function renderFootballGlos() {
        $articles = $this->articles->getArticlesPagiCategory('tips');
        $this->presenter->visualPaginatorFunction($articles);
    }

    public function createComponentContactUs() {
        $form = new \AdminForm();

        $form->addText('name', 'Zadejte jméno')
                ->setRequired('Vyplňte jméno.');

        $form->addText('surname', 'Zadejte příjmení')
                ->setRequired('Vyplňte příjmení.');

        $form->addText('email', 'Zadejte email')
                ->setRequired('Vyplňte email.');

        $form->addTextArea('content', 'Obsah zprávy')
                ->setRequired('Zadejte obsah.');

        $form->addSubmit('send', 'Odeslat');

        $form->onSuccess[] = [$this, 'contactUsSuccessed'];

        return $form;
    }

    public function contactUsSuccessed(\AdminForm $form) {
        $values = $form->getValues();
//        try {
        $params = array(
            'name' => $values->name,
            'surname' => $values->surname,
            'email' => $values->email,
            'content' => $values->content
        );
        \Tracy\Debugger::barDump($params);
        $this->question->sendEmail($params);
        $this->question->insert($params);
        $this->flashMessage('Dotaz byl úspěšně odeslán', 'success');
        $this->redirect('this');
//        } catch (\PDOException $e) {
//            $this->flashMessage('Nepodařilo se připojit k databázi.', 'danger');
//        }
    }

}
