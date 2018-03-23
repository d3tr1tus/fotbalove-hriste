<?php

namespace App\Forms;

use Nette;
use Nette\Application\UI\Form;
use App\Model;

class SignUpFormFactory {

    use Nette\SmartObject;

    /** @var Nette\Application\LinkGenerator */
    private $linkGenerator;

    /** @var Nette\Bridges\ApplicationLatte\ILatteFactory */
    private $latteFactory;
    private $mailer;

    const PASSWORD_MIN_LENGTH = 7;

    /** @var FormFactory */
    private $factory;

    /** @var Model\UserManager */
    private $userManager;

    public function __construct(FormFactory $factory, Model\UserManager $userManager, \Nette\Mail\IMailer $mailer, Nette\Application\LinkGenerator $lg, \Nette\Bridges\ApplicationLatte\ILatteFactory $latteFactory) {
        $this->factory = $factory;
        $this->userManager = $userManager;
        $this->mailer = $mailer;
        $this->linkGenerator = $lg;
        $this->latteFactory = $latteFactory;
    }

    /**
     * @return Form
     */
    public function create(callable $onSuccess) {
        $form = new \AdminForm();
        $form->addText('first_name', 'Jméno:')
                ->setRequired('Zadejte své jméno.');

        $form->addText('last_name', 'Příjmení:')
                ->setRequired('Zadejte své příjmení.');

        $form->addText('email', 'Váš email:')
                ->setRequired('Vyplňte svůj email.')
                ->addRule($form::EMAIL);

        $form->addText('tel', 'Vaše telefoní číslo:')
                ->setRequired('Vyplňte Vaše telefoní číslo.');

        $form->addPassword('password', 'Zvolte si heslo:')
                ->setOption('description', sprintf('nejméně %d znaků', self::PASSWORD_MIN_LENGTH))
                ->setRequired('Zvolte si heslo.')
                ->addRule($form::MIN_LENGTH, NULL, self::PASSWORD_MIN_LENGTH);

        $form->addPassword('password_valid', 'Heslo znovu:')
                ->addRule(Form::EQUAL, "Hesla se musí shodovat!", $form['password'])
                ->setRequired('Zadejte heslo znovu.');


        $form->addSubmit('send', 'Registrovat');

        $form->onSuccess[] = function (Form $form, $values) use ($onSuccess) {
            try {
                $randomUrl = $this->generateRandomString();
                $params = array(
                    'name' => $values->first_name . ' ' . $values->last_name,
                    'url' => $randomUrl,
                    'email' => $values->email
                );
                $this->sendEmail($params);
                $this->userManager->add($values->first_name, $values->last_name, $values->email, $values->password, $values->tel, $randomUrl);
            } catch (Model\DuplicateEmailException $e) {
                $form->addError('Tento email je již registrován. Zadejte jiný.');
                return;
            }
            $onSuccess();
        };
        return $form;
    }

    public function sendEmail(array $params) {
        $latte = $this->latteFactory->create();
        Nette\Bridges\ApplicationLatte\UIMacros::install($latte->getCompiler());
        $latte->addProvider('uiControl', $this->linkGenerator);

        $html = $latte->renderToString(__DIR__ . '/../components/mailSender.latte', $params);

        $mail = new Nette\Mail\Message;
        $mail->setFrom('Team fotbalhriste.cz <info@fotbalhriste.cz>');
        $mail->addTo($params['email'], $params['name']);
        $mail->setHtmlBody($html);

        $this->mailer->send($mail);
    }

    public function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public function confirmEmail($url) {
        $this->userManager->confirmEmail($url);
    }

}
