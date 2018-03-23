<?php

use Models\ModelException;

namespace App\Model;

/**
 *
 * @author Filip Březina <filip.brezina11@gmail.com>
 */
class Question extends BaseFactory {

    /** @var Nette\Application\LinkGenerator */
    private $linkGenerator;

    /** @var Nette\Bridges\ApplicationLatte\ILatteFactory */
    private $latteFactory;
    private $mailer;

    public function __construct(\Nette\Database\Context $database, \Nette\Mail\IMailer $mailer, \Nette\Application\LinkGenerator $lg, \Nette\Bridges\ApplicationLatte\ILatteFactory $latteFactory) {
        $this->table = 'question';
        $this->mailer = $mailer;
        $this->linkGenerator = $lg;
        $this->latteFactory = $latteFactory;
        parent::__construct($database);
    }

    public function sendEmail(array $params) {
        $latte = $this->latteFactory->create();
        \Nette\Bridges\ApplicationLatte\UIMacros::install($latte->getCompiler());
        $latte->addProvider('uiControl', $this->linkGenerator);
        \Tracy\Debugger::barDump(__DIR__ . '/../components/mailContact.latte');
        $html = $latte->renderToString(__DIR__ . '/../components/mailContact.latte', $params);

        $mail = new \Nette\Mail\Message;
        $mail->setFrom($params['email'], $params['name']);
        $mail->addTo('Team fotbalhriste.cz <info@fotbalhriste.cz>');
        $mail->setHtmlBody($html);

        $this->mailer->send($mail);
    }

    public function getQuestions() {
        return $this->db->table($this->table)
                        ->where('opened', NULL)
                        ->count();
    }
    
    public function opened() {
        $this->db->table($this->table)
                ->where('opened', NULL)
                ->update(array(
                    'opened' => new \DateTime()
                ));
    }

}
