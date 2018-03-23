<?php

namespace App\Model;

use Nette;
use Nette\Security\Passwords;

/**
 * Users management.
 */
class UserManager extends Nette\Object implements Nette\Security\IAuthenticator {

    use Nette\SmartObject;

    const
            TABLE_NAME = 'user',
            COLUMN_ID = 'id_user',
            COLUMN_FIRST_NAME = 'first_name',
            COLUMN_LAST_NAME = 'last_name',
            COLUMN_TEL = 'tel',
            COLUMN_ROLE_ID = 'role_id',
            COLUMN_PASSWORD_HASH = 'password',
            COLUMN_EMAIL = 'email',
            COLUMN_URL = 'url',
            COLUMN_ROLE = 'name';

    /** @var Nette\Database\Context */
    private $database;

    public function __construct(Nette\Database\Context $database) {
        $this->database = $database;
    }

    /**
     * Performs an authentication.
     * @return Nette\Security\Identity
     * @throws Nette\Security\AuthenticationException
     */
    public function authenticate(array $credentials) {
        list($email, $password) = $credentials;

        $row = $this->database->table(self::TABLE_NAME)->select('*')->select('role.name')->where(self::COLUMN_EMAIL, $email)->fetch();

        if (!$row) {
            throw new Nette\Security\AuthenticationException('The username is incorrect.', self::IDENTITY_NOT_FOUND);
        } elseif (!Passwords::verify($password, $row[self::COLUMN_PASSWORD_HASH])) {
            throw new Nette\Security\AuthenticationException('The password is incorrect.', self::INVALID_CREDENTIAL);
        } elseif (Passwords::needsRehash($row[self::COLUMN_PASSWORD_HASH])) {
            $row->update([
                self::COLUMN_PASSWORD_HASH => Passwords::hash($password),
            ]);
        }
        $arr = $row->toArray();
        unset($arr[self::COLUMN_PASSWORD_HASH]);
        return new Nette\Security\Identity($row[self::COLUMN_ID], $row[self::COLUMN_ROLE], $arr);
    }

    /**
     * Adds new user.
     * @param  string
     * @param  string
     * @param  string
     * @return void
     * @throws DuplicateNameException
     */
    public function add($first_name, $last_name, $email, $password, $tel, $url) {
        try {
            if ($this->database->table(self::TABLE_NAME)->where('email', $email)->fetchField()) {
                throw new DuplicateEmailException;
            }
            $this->database->table(self::TABLE_NAME)->insert([
                self::COLUMN_FIRST_NAME => $first_name,
                self::COLUMN_LAST_NAME => $last_name,
                self::COLUMN_PASSWORD_HASH => Passwords::hash($password),
                self::COLUMN_TEL => $tel,
                self::COLUMN_EMAIL => $email,
                self::COLUMN_URL => $url
            ]);
        } catch (Nette\Database\UniqueConstraintViolationException $e) {
            throw new DuplicateNameException;
        }
    }

    public function confirmEmail($url) {
        $this->database->table(self::TABLE_NAME)
                ->where('url', $url)
                ->update([
                    'confirm' => new Nette\Utils\DateTime()
        ]);

        return TRUE;
    }
    
    public function isEmailConfirm($user_id) {
        return $this->database->table(self::TABLE_NAME)
                ->select('confirm')
                ->where('id_user', $user_id)
                ->fetchField();
    }

}

class DuplicateNameException extends \Exception {
    
}

class DuplicateEmailException extends \Exception {
    
}
