<?php

namespace App\FrontModule\Presenters;

use Nette;
use App\Model;

class AddPitchPresenter extends BasePresenter {

    /**
     *
     * @var PageSession 
     */
    public $pageSession;
    public $factory;

    public function startup() {
        $this->pageSession = $this->getSession('PageSession');
        $this->pageSession->page;
        parent::startup();
    }

    public function beforeRender() {
        if (!$this->user->isLoggedIn()) {
            $this->redirect('Sign:in');
        }
        if ($this->user->isInRole(\Constants::ROLE_ADMIN)) {
            $this->flashMessage('Hřiště prosím upravujte zde.', 'info');
            $this->redirect(':Admin:Homepage:allPitches');
        }
        if ($this->userData->getActivatedAccount($this->user->getId())->confirm == NULL) {
            $this->flashMessage('Váš účet nebyl dosud aktivován. Učiňte tak během následujících dní.', 'danger');
        }
        parent::beforeRender();
    }

    public function renderDefault() {
        $this->template->pitch = $this->pitch->initID($this->pitch->getPitch($this->user->getId()));
        $this->template->page = $this->pageSession->page;
    }

    public function createComponentAddPitchForm() {
        $form = new \AdminForm();
        $form->addText('name', 'Název hřiště')
                ->setRequired('Zadejte název hřiště');
        $form->addTextArea('info', 'Krátký popis')
                ->setRequired(FALSE)
                ->addRule(\Nette\Forms\Form::MAX_LENGTH, 'Překročili jste maximální délku textu %d', 320);
        $form->addText('adress', 'Adresa')
                ->setRequired('Zadejte adresu');
        $form->addText('admin', 'Správce')
                ->setRequired('Zadejte správce hřiště');
        $form->addText('contact_club', 'Kontakt na klub')
                ->setRequired('Zadejte kontakt na klub');
        $form->addText('name_contact', 'Kontaktní osoba')
                ->setRequired('Zadejte kontaktní osobu');
        $form->addText('tel', 'Telefon')
                ->setRequired('Zadejte telefon');

        $form->addSelect('region', 'Vyberte kraj:', \Constants::$regions)
                ->setPrompt('Vyberte kraj')
                ->setRequired('Vyberte okres');

        if ($this->pitch->getPitch($this->user->getId())) {
            $this->pitch->initID($this->pitch->getPitch($this->user->getId()));
            $form->setDefaults(array(
                'name' => $this->pitch->get('name'),
                'info' => $this->pitch->get('info'),
                'adress' => $this->pitch->get('adress'),
                'admin' => $this->pitch->get('admin'),
                'contact_club' => $this->pitch->get('contact_club'),
                'name_contact' => $this->pitch->get('name_contact'),
                'adress' => $this->pitch->get('adress'),
                'tel' => $this->pitch->get('tel'),
                'region' => $this->pitch->get('region')
            ));
        }

        $form->addHidden('user_id', $this->user->getId());
        $form->addSubmit('send', 'Další');

        $form->onSuccess[] = [$this, 'addPitchFormSuccessed'];

        return $form;
    }

    public function addPitchFormSuccessed(\AdminForm $form) {
        $values = $form->getValues();
        try {
            if ($this->pitch->getPitch($this->user->getId())) {
                $this->pitch->update($values);
                $this->flashMessage('$message');
                $this->pageSession->page = $this->pageSession->page + 1;
                $this->redrawControl('addPitch');
            } else {
                $this->pitch->add($values);
                $this->flashMessage('$message');
                $this->pageSession->page = $this->pageSession->page + 1;
                $this->redrawControl('addPitch');
            }
        } catch (\PDOException $e) {
            $this->flashMessage('Nepodařilo se připojit k databázi.');
        }
    }

    protected function createComponentUploadImage() {
        $form = new \AdminForm();

        $this->pitch->initID($this->pitch->getPitch($this->user->getId()));
        $form->addFileUpload("image")
                ->setParams([
                    'user_id' => $this->user->getId(),
                    'pitch_id' => $this->pitch->getID(),
                    'type' => 'pitch'
        ]);

        return $form;
    }

    public function handleToggleChangePage($type = NULL) {
        if ($type == 'next') {
            if ($this->pageSession->page == 1 && !$this->pitch->getMainImage()) {
                $this->flashMessage('Musíte přidat alespoň jeden obrázek', 'danger');
            }
            $this->pageSession->page = $this->pageSession->page + 1;
        } else {
            $this->pageSession->page = $this->pageSession->page - 1;
        }
        $this->redrawControl('addPitch');
    }

    public function handleCheckboxWeHave($name, $type = NULL) {
        $this->pitch->initID($this->pitch->getPitch($this->user->getId()));
        $this->pitch->changeCheckboxWeHave($name, $type);
        $this->redrawControl('weHave');
    }

    public function createComponentTeamForm() {
        $form = new \AdminForm();

        $this->pitch->initID($this->pitch->getPitch($this->user->getId()));
        $form->addHidden('name', $this->pitch->get('name'));
        $form->addHidden('pitch_id', $this->pitch->getID());
        $form->addSelect('category', 'Kategorie', \Constants::$team_kategory);
        $form->addText('competition', 'Liga');

        $form->addSubmit('save', '+ Přidat');

        $form->onSuccess[] = [$this, 'teamFormSuccessed'];

        return $form;
    }

    public function teamFormSuccessed(\AdminForm $form) {
        $this->pitch->initID($this->pitch->getPitch($this->user->getId()));
        $values = $form->getValues();
        try {
            $this->pitch->addTeam($values);
            $this->redrawControl('team');
        } catch (\PDOException $e) {
            $this->flashMessage('Nepodařilo se připojit k databázi.');
        }
    }

    public function handleAddTeam() {
        $this->redrawControl('team');
    }

    public function handleDeleteTeam() {
        $this->pitch->initID($this->pitch->getPitch($this->user->getId()));
        $this->pitch->deleteTeam();
        $this->redrawControl('team');
    }

    public function handleCompleteAddPitch() {
        $this->pageSession->page = 0;
        $this->redirect('Homepage:default');
    }

    public function handleDeleteImage($image_id) {
        $this->pitch->deleteImage($image_id);
        $this->redrawControl('image');
    }

    public function createComponentCoordinatesForm() {
        $form = new \AdminForm();

        $form->addText('lat', 'Latitude');

        $form->addText('long', 'Longitude');

        $form->addHidden('pitch_id', $this->pitch->getID());

        $form->addSubmit('save', 'Uložit souřadnice');

        if ($this->pitch->getCoordinates()) {
            $form->setDefaults([
                'lat' => $this->pitch->getCoordinates()->lat,
                'long' => $this->pitch->getCoordinates()->long
            ]);
        }

        $form->onSuccess[] = [$this, 'coordinatesFormSuccessed'];

        return $form;
    }

    public function coordinatesFormSuccessed(\AdminForm $form) {
        $values = $form->getValues();
        try {
            $this->pitch->addCoordinates($values);
        } catch (PDOException $e) {
            $this->flashMessage('Nepodařilo se připojit k databázi.');
        }
    }

    protected function createComponentUploadCharacter() {
        $form = new \AdminForm();

        $this->pitch->initID($this->pitch->getPitch($this->user->getId()));
        $form->addFileUpload("imageCharacter")
                ->setParams([
                    'user_id' => $this->user->getId(),
                    'pitch_id' => $this->pitch->getID(),
                    'type' => 'character'
                ])
                ->setMaxFiles(1);

        return $form;
    }

}
