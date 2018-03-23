<?php

namespace App\AdminModule\Presenters;

use Nette;
use App\Model;

class AddPitchPresenter extends BasePresenter {

    /**
     *
     * @var PageSession 
     */
    public $pageSessionAdmin;
    public $factory;

    public function startup() {
        $this->pageSessionAdmin = $this->getSession('PageSessionAdmin');
        $this->pageSessionAdmin->page;
        parent::startup();
    }

    public function beforeRender() {
        parent::beforeRender();
    }

    public function renderDefault($pitch_id = NULL) {
        $pitch_id = $this->pitch->initID($this->pitch->getPitch($this->user->getId()));
        $this->template->pitch = $pitch_id;
        $this->template->page = $this->pageSessionAdmin->page;
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

        if ($this->getParameter('pitch_id')) {
            $this->pitch->initID($this->getParameter('pitch_id'));
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
            $this->pitch->add($values);
            $this->flashMessage('$message');
            $this->pageSessionAdmin->page = $this->pageSessionAdmin->page + 1;
            $this->redrawControl('addPitch');
        } catch (\Exception $e) {
            $this->flashMessage('Něco se pokazilo prosím kontaktujte správce');
        }
    }

    protected function createComponentUploadImage() {
        $form = new \AdminForm();

        $this->pitch->initID($this->getParameter('pitch_id'));
        $form->addFileUpload("image")
                ->setParams([
                    'user_id' => $this->user->getId(),
                    'pitch_id' => $this->pitch->getID()
        ]);
        $form->addSubmit('send', 'Další');

        $form->onSuccess[] = [$this, 'uploadImageSuccessed'];

        return $form;
    }

    public function uploadImageSuccessed(\AdminForm $form) {
        $values = $form->getValues();
        $this->pageSessionAdmin->page = $this->pageSessionAdmin->page + 1;
        $this->redrawControl('addPitch');
    }

    public function handleCheckboxWeHave($name, $type = NULL) {
        $this->pitch->initID($this->getParameter('pitch_id'));
        $this->pitch->changeCheckboxWeHave($name, $type);
        $this->redrawControl('weHave');
    }

    public function createComponentTeamForm() {
        $form = new \AdminForm();

        $this->pitch->initID($this->getParameter('pitch_id'));
        $form->addHidden('name', $this->pitch->get('name'));
        $form->addHidden('pitch_id', $this->pitch->getID());
        $form->addSelect('category', 'Kategorie', \Constants::$team_kategory);
        $form->addText('competition', 'Liga');

        $form->addSubmit('save', '+ Přidat');

        $form->onSuccess[] = [$this, 'teamFormSuccessed'];

        return $form;
    }

    public function teamFormSuccessed(\AdminForm $form) {
        $this->pitch->initID($this->getParameter('pitch_id'));
        $values = $form->getValues();
        try {
            $this->pitch->addTeam($values);
            $this->redrawControl('team');
        } catch (\Exception $e) {
            $this->flashMessage('Něco se pokazilo prosím kontaktujte správce');
        }
    }

    public function handleAddTeam() {
        $this->redrawControl('team');
    }

    public function handleDeleteTeam() {
        $this->pitch->initID($this->getParameter('pitch_id'));
        $this->pitch->deleteTeam();
        $this->redrawControl('team');
    }

    public function handleCompleteAddPitch() {
        $this->pageSessionAdmin->page = 0;
        $this->redirect('Homepage:default');
    }

    public function handleDeleteImage($image_id) {
        $this->pitch->deleteImage($image_id);
        $this->redrawControl('image');
    }

    public function handleNextPage() {
        $this->pageSessionAdmin->page = $this->pageSessionAdmin->page + 1;
        $this->redrawControl('addPitch');
    }

}
