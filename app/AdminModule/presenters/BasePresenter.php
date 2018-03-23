<?php

/**
 *
 * @author Filip Březina <filip.brezina11@gmail.com>
 */

namespace App\AdminModule\Presenters;

use Nette;
use App\Model;

/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends Nette\Application\UI\Presenter {

    /**
     *
     * @var Pitch 
     */
    public $pitch;

    /**
     *
     * @var Articles 
     */
    public $article;
    public $articles;

    /**
     *
     * @var Question
     */
    public $question;

    /**
     *
     * @var Question
     */
    public $newsletter;

    public function startup() {
        parent::startup();
        \Tracy\Debugger::$maxDepth = 20;
        if (!$this->user->isLoggedIn()) {
            $this->redirect(':Front:Sign:in');
        }
        if (!$this->user->isInRole(\Constants::ROLE_ADMIN)) {
            $this->flashMessage('Do této sekce nemáte přístup!', 'danger');
            $this->redirect(':Front:Sign:in');
        }

        $this->pitch = $this->context->getService('pitch');
        $this->article = $this->context->getService('article');

        $this->articles = $this->context->getService('articleModel');
        $this->question = $this->context->getService('question');
        $this->newsletter = $this->context->getService('newsletter');
    }

    public function beforeRender() {
        parent::beforeRender();
        $this->template->pitch = $this->pitch;
        $this->template->articles = $this->articles;
        $this->template->question = $this->question;

        if ($this->question->getQuestions()) {
            $this->flashMessage('Máte ' . $this->question->getQuestions() . ' nepřečtené zprávy přejděte prosím do emailu.');
        }
    }

    public function renderDefault() {
        
    }

    public function handleResultPitch($pitch_id, $result = NULL) {
        $this->pitch->initID($pitch_id);
        if ($result == 'yes') {
            $this->pitch->set('accepted', new Nette\Utils\DateTime());
            $this->pitch->addToCompetition();
        } else {
            $this->pitch->set('declined', new Nette\Utils\DateTime());
        }
        $this->redirect('this');
    }

    public function handleReaded() {
        $this->question->opened();
        $this->redirect('this');
    }

}
