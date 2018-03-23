<?php

namespace App\FrontModule\Presenters;

use Nette;
use App\Forms;
use Nette\Utils\DateTime;

class CompetitionPresenter extends BasePresenter {
    
    /**
     *
     * @var Http Session 
     */
    public $httpSession;
    
    /**
     *
     * @var Http request 
     */
    public $httpRequest;
    
    /**
     *
     * @var Date 
     */
    public $date;

    public function startup() {
        parent::startup();
        
        $this->httpRequest = $this->getHttpRequest();
        $this->httpSession = $this->getSession('httpSession');
        $this->httpSession->http;
    }

    public function beforeRender() {
        parent::beforeRender();
        $this->template->pitch = $this->pitch;
        $this->template->httpSession = $this->httpSession->http;
        $this->template->date = DateTime::createFromFormat('mj', new Nette\Utils\DateTime());
        \Tracy\Debugger::barDump($this->httpSession->http);
        \Tracy\Debugger::barDump($this->httpRequest->getRemoteAddress());
    }

    public function handleVoting($pitch_id) {
        $this->pitch->addVote($pitch_id);
        $this->httpSession->http = $this->httpRequest->getRemoteAddress();
        $this->flashMessage('Hlasování proběhlo úspěšně', 'success');
        $this->redirect('this');
    }

}
