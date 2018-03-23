<?php

namespace App\Model;

use Models\ModelException;
use ondrs\UploadManager\Upload;

/**
 *
 * @author Filip Březina <filip.brezina11@gmail.com>
 */
class UploadModel extends BaseModel implements \Zet\FileUpload\Model\IUploadModel {

    /** @var Nette\Database\Context @inject */
    public $db;

    /** @var \ondrs\UploadManager\Upload @inject */
    public $upload;

    public function __construct(\ondrs\UploadManager\Upload $upload, \Nette\Database\Context $database) {
        $this->upload = $upload;
        $this->db = $database;
    }

    public function remove($uploaded) {
        $name = str_replace('_', '-', $uploaded[0]->name);

        $this->db->table('image')
                ->where('url LIKE', '%' . $name . '%')
                ->fetch()
                ->delete();
    }

    public function rename($upload, $newName) {
        $name = str_replace('_', '-', $upload[0]->name);

        $this->db->table('image')
                ->where('name LIKE', '%' . $name . '%')
                ->update([
                    'name' => $newName
        ]);
    }

    public function save(\Nette\Http\FileUpload $file, array $params = array()) {
        if ($params['type'] == 'article') {
            $this->upload->filesToDir('/article');
        } elseif($params['type'] == 'character') {
            $this->upload->filesToDir('/characters');
            
            $this->db->table('character')->insert(array(
                'pitch_id' => $params['pitch_id'],
                'name' => $file->getSanitizedName(),
                'url' => 'uploads/characters/800_' . $file->getName(),
                'thumb' => 'uploads/characters/thumb_' . $file->getName()
            ));
        } else {
            $this->upload->filesToDir('/user_id_' . $params['user_id']);

            $max = $this->db->table('image')
                    ->where('pitch_id', $params['pitch_id'])
                    ->max('number');

            $number = $max ? $max + 1 : 1;

            $file->getName();

            $this->db->table('image')->insert(array(
                'pitch_id' => $params['pitch_id'],
                'name' => $file->getSanitizedName(),
                'url' => 'uploads/user_id_' . $params['user_id'] . '/800_' . $file->getName(),
                'thumb' => 'uploads/user_id_' . $params['user_id'] . '/thumb_' . $file->getName(),
                'number' => $number
            ));
        }

        return array($file);
    }

}
