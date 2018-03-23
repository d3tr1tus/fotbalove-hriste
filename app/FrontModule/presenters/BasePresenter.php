<?php

/**
 *
 * @author Filip Březina <filip.brezina11@gmail.com>
 */

namespace App\FrontModule\Presenters;

use Nette;
use App\Model;

/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends Nette\Application\UI\Presenter {

    /**
     *
     * @var Articles 
     */
    public $article;
    public $articles;

    /**
     *
     * @var Pitch 
     */
    public $pitch;

    /**
     *
     * @var Search
     */
    public $search;

    /**
     *
     * @var Question
     */
    public $question;

    /**
     *
     * @var Newsletter
     */
    public $newsletter;

    /**
     *
     * @var Password coming soon 
     */
    public $comingSoonPass;

    /**
     *
     * @var Password coming soon 
     */
    public $countDownEnd;

    /**
     *
     * @var UserData 
     */
    public $userData;

    public function startup() {
        parent::startup();
        \Tracy\Debugger::$maxDepth = 50;
        $this->article = $this->context->getService('article');
        $this->search = $this->context->getService('search');
        $this->pitch = $this->context->getService('pitch');
        $this->userData = $this->context->getService('userData');
        $this->question = $this->context->getService('question');
        $this->newsletter = $this->context->getService('newsletter');

        $this->articles = $this->context->getService('articleModel');

        $this->comingSoonPass = $this->getSession('ComingSoon');
        $this->comingSoonPass->pass;

        $this->countDownEnd = $this->getSession('CountDownEnd');
        $this->countDownEnd->end;
    }

    protected function beforeRender() {
        parent::beforeRender();
        $this->template->soon = $this->comingSoonPass->pass;
        $this->template->countDownEnd = $this->countDownEnd->end;
    }

    public function createComponentSearchForm() {
        $form = new \AdminForm();
        $form->addText('text', 'Zadejte hledané slovo');
        $form->addSubmit('find', '');

        $form->onSuccess[] = [$this, 'searchFormSuccessed'];

        return $form;
    }

    public function searchFormSuccessed(\AdminForm $form) {
        $values = $form->getValues();
        $this->search->getSearchPitches($values['text']);
        $this->redirect('Search:default', $values['text']);
    }

    public function handleFilterByRegion($region) {
        $r = $this->search->filterByRegion($region);
        $this->redirect('Search:default', $q = NULL, $region);
    }

    public function countTeamsInRegion($region) {
        return $this->search->countTeamsInRegion($region);
    }

    public function createComponentSoonForm() {
        $form = new \AdminForm();

        $form->addPassword('soon_pass', 'Zadejte heslo');

        $form->addSubmit('send', 'Odeslat');

        $form->onSuccess[] = [$this, 'soonFormSuccessed'];

        return $form;
    }

    public function soonFormSuccessed(\AdminForm $form) {
        $values = $form->getValues();
        $this->comingSoonPass->pass = $values['soon_pass'];
        $this->redirect('this');
    }

    public function handleRefresh() {
        $this->countDownEnd->end = TRUE;
        $this->redirect('this');
    }

    public function getLastThreeTeams() {
        return $this->search->getLastThreeTeams();
    }

    public function createComponentNewsletterForm() {

        $form = new \AdminForm();

        $form->addText('email')
                ->setRequired('Musíte zadat email');

        $form->onSuccess[] = [$this, 'newsletterFormSucceeded'];

        $form->addSubmit('send');

        return $form;
    }

    public function newsletterFormSucceeded(\AdminForm $form) {
        $values = $form->getValues();
        try {
            $this->newsletter->addNewsletterCustomer($values);
            $this->flashMessage('Přihlásili jste se k odběru novinek');
            $this->redirect('this');
        } catch (\PDOException $e) {
            $this->flashMessage('Nepodařilo se připojit k databázi.');
            $this->redirect('this');
        }
    }

}
