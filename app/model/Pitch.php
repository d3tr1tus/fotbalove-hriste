<?php

use Models\ModelException;

namespace App\Model;

/**
 *
 * @author Filip Březina <filip.brezina11@gmail.com>
 */
class Pitch extends BaseFactory {

    public function __construct(\Nette\Database\Context $database) {
        $this->table = 'pitch';
        parent::__construct($database);
    }

    public function getMainImage() {
        return $this->db->table('image')
                        ->select('url')
                        ->where('pitch_id', $this->getID())
                        ->where('number', 1)
                        ->fetchField();
    }

    public function getImages() {
        return $this->db->table('image')
                        ->where('pitch_id', $this->getID())
                        ->where('NOT number', 1)
                        ->order('number')
                        ->fetchAll();
    }

    public function getAllImages() {
        return $this->db->table('image')
                        ->where('pitch_id', $this->getID())
                        ->order('number')
                        ->fetchAll();
    }

    public function getTeams() {
        return $this->db->table('team')
                        ->where('pitch_id', $this->getID())
                        ->fetchAll();
    }

    public function getWeHave() {
        return $this->db->table('we_have')
                        ->select('*')
                        ->where('pitch_id', $this->getID())
                        ->order('value')
                        ->fetchAll();
    }

    public function add($data) {
        $this->db->table($this->table)->insert($data);
    }

    public function getPitch($user_id) {
        $query = $this->db->table($this->table)
                ->select('id_pitch')
                ->where('user_id', $user_id)
                ->order('created DESC')
                ->fetchField();

        return $query;
    }

    public function existWeHaveItem($name) {
        return $this->db->table('we_have')
                        ->select('exist')
                        ->where('pitch_id', $this->getID())
                        ->where('name', $name)
                        ->fetchField();
    }

    public function changeCheckboxWeHave($name, $type = NULL) {
        $q = $this->db->table('we_have')
                ->where('name', $name)
                ->where('pitch_id', $this->getID());
        if ($type == 'yes') {
            if (!$q->fetch()) {
                $this->addWeHave();
            }
            $q->update(array(
                'exist' => 1
            ));
        } else {
            if (!$q->fetch()) {
                $this->addWeHave();
            }
            $q->update(array(
                'exist' => NULL
            ));
        }
    }

    public function addWeHave() {
        foreach (\Constants::$we_have as $key => $value) {
            $query = $this->db->table('we_have')
                    ->where('pitch_id', $this->getID())
                    ->where('name', $key)
                    ->fetch();
            if (!$query) {
                $this->db->table('we_have')
                        ->insert(array(
                            'name' => $key,
                            'value' => $value,
                            'exist' => NULL,
                            'pitch_id' => $this->getID()
                ));
            }
        }
    }

    public function addTeam($values) {
        $this->db->table('team')->insert($values);
    }

    public function deleteTeam() {
        $this->db->table('team')
                ->where('pitch_id', $this->getID())
                ->fetch()
                ->delete();
    }

    public function deleteImage($image_id) {
        $this->db->table('image')
                ->where('id_image', $image_id)
                ->fetch()
                ->delete();
    }

    public function getNewPitches() {
        $query = $this->db->table($this->table)
                ->where('accepted', NULL)
                ->where('declined', NULL)
                ->fetchAll();

        return $this->createInstance('Pitch')->arrayToObject($query);
    }

    public function getNewestPitch() {
        $q = $this->db->table($this->table)
                ->order('created DESC')
                ->fetch();

        return $this->createInstance('Pitch')->initID($q);
    }

    public function getAllPitches() {
        $q = $this->db->table('image')
                ->select('thumb')
                ->select('pitch.*')
                ->where('number', 1)
                ->order('created DESC')
                ->fetchAll();

        return $this->createInstance('Pitch')->arrayToObject($q);
    }

    public function deletePitch($pitch_id) {
        $this->db->table($this->table)
                ->where('id_pitch', $pitch_id)
                ->fetch()
                ->delete();
    }

    public function isConfirmEmail() {
        return $this->db->table($this->table)
                        ->select('id_pitch')
                        ->select('user.*')
                        ->where('id_pitch', $this->getID())
                        ->fetch();
    }

    public function addCoordinates($values) {
        $this->db->table('coordinate')
                ->insert($values);
    }

    public function getCoordinates() {
        return $this->db->table('coordinate')
                        ->where('pitch_id', $this->getID())
                        ->order('created DESC')
                        ->fetch();
    }

    public function getCharacter() {
        return $this->db->table('character')
                        ->select('*')
                        ->where('pitch_id', $this->getID())
                        ->order('created DESC')
                        ->fetch();
    }

    public function addToCompetition() {
        $this->db->table('competition')->insert(array(
            'pitch_id' => $this->getID()
        ));
    }

    public function getResultCompetition() {
        $q = $this->db->table('competition')
                ->select('vote')
                ->select('pitch.*')
                ->order('vote DESC')
                ->fetchAll();

        return $this->createInstance('Pitch')->arrayToObject($q);
    }

    public function getPercentageVote($vote) {
        $allAdmin = 0;

        foreach ($this->getResultCompetition() as $allVotes) {
            $allAdmin = $allAdmin + $allVotes->get('vote');
        }

        if ($allAdmin == 0) {
            return 0;
        } else {
            return ($vote / $allAdmin) * 100;
        }
    }

    public function addVote($pitch_id) {
        $actuallVote = $this->db->table('competition')
                ->where('pitch_id', $pitch_id)
                ->fetchField('vote');

        $this->db->table('competition')
                ->where('pitch_id', $pitch_id)
                ->update(array(
                    'vote' => $actuallVote + 1
        ));
    }

}
