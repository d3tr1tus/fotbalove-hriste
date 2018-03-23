<?php

namespace App\FrontModule\Presenters;

use Nette;
use App\Model;

class SearchPresenter extends BasePresenter {

    public function renderDefault($q = NULL, $region = NULL) {
        $this->template->search = $this->search->getSearchPitches($q, $region);
        $this->template->searchModel = $this->search;
        $this->template->query = $q;
        $this->template->region = $region;
    }
    
    public function renderPitchCard($q = NULL, $pitch_card_id) {
        $this->pitch->initID($pitch_card_id);
        if ($this->pitch->get('accepted') == NULL && !$this->user->isInRole(\Constants::ROLE_ADMIN)) {
            $this->flashMessage('Sem nemáte přístup', 'danger');
            $this->redirect('Homepage:default');
        }
        $this->template->pitch = $this->pitch;
        $this->template->query = $q;
    }

}
