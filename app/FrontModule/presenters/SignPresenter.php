<?php

namespace App\FrontModule\Presenters;

use Nette;
use App\Forms;

class SignPresenter extends BasePresenter {

    /** @var Forms\SignInFormFactory @inject */
    public $signInFactory;

    /** @var Forms\SignUpFormFactory @inject */
    public $signUpFactory;

    /**
     * Sign-in form factory.
     * @return Nette\Application\UI\Form
     */
    protected function createComponentSignInForm() {
        return $this->signInFactory->create(function () {
                    if ($this->user->isInRole(\Constants::ROLE_ADMIN)) {
                        $this->redirect(':Admin:Homepage:default');
                    } else {
                        $this->redirect('AddPitch:');
                    }
                });
    }

    /**
     * Sign-up form factory.
     * @return Nette\Application\UI\Form
     */
    protected function createComponentSignUpForm() {
        return $this->signUpFactory->create(function () {
                    $this->redirect('AddPitch:');
                });
    }

    public function actionOut() {
        $this->getUser()->logout(TRUE);
        $this->flashMessage('Byli jste odhlášeni', 'success');
        $this->redirect('in');
    }
    
    public function renderConfirm($url) {
        if ($url == NULL) {
            $this->flashMessage('Sem nemáte přístup!', 'danger');
            $this->redirect('in');
        }
        $this->signUpFactory->confirmEmail($url);
    }

}
