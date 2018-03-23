<?php

use Models\ModelException;

namespace App\Model;

/**
 *
 * @author Filip Březina <filip.brezina11@gmail.com>
 */
class Newsletter extends BaseFactory {

    /** @var Nette\Application\LinkGenerator */
    private $linkGenerator;

    /** @var Nette\Bridges\ApplicationLatte\ILatteFactory */
    private $latteFactory;

    /* Mailer */
    private $mailer;

    public function __construct(\Nette\Database\Context $database, \Nette\Mail\IMailer $mailer, \Nette\Application\LinkGenerator $lg, \Nette\Bridges\ApplicationLatte\ILatteFactory $latteFactory) {
        $this->table = 'newsletter';
        $this->mailer = $mailer;
        $this->linkGenerator = $lg;
        $this->latteFactory = $latteFactory;
        parent::__construct($database);
    }

    public function sendEmail(array $params, $type) {
        $latte = $this->latteFactory->create();
        \Nette\Bridges\ApplicationLatte\UIMacros::install($latte->getCompiler());
        $latte->addProvider('uiControl', $this->linkGenerator);

        if ($type == 'newsletter') {
            $html = $latte->renderToString(__DIR__ . '/../components/mailNewsletter.latte', $params);
        }
        
        $allCustomers = $this->db->table($this->table)->fetchAll();

        $mail = new \Nette\Mail\Message();
        $mail->setFrom('Team fotbalhriste.cz <info@fotbalhriste.cz>');
        foreach ($allCustomers as $customer) {
            $mail->addTo($customer->email);
        }
        $mail->setHtmlBody($html);

        $this->mailer->send($mail);
    }
    
    public function addNewsletterCustomer($values) {
        $this->db->table($this->table)->insert($values);
    }

}
