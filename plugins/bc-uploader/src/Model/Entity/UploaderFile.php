<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */

namespace BcUploader\Model\Entity;

use Cake\I18n\FrozenTime;
use Cake\ORM\Entity;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * Class UploaderFile
 *
 * @property int $id
 * @property string $name
 * @property string $alt
 * @property int $uploader_category_id
 * @property int $user_id
 * @property FrozenTime $publish_begin
 * @property FrozenTime $publish_end
 * @property FrozenTime $created
 * @property FrozenTime $modified
 */
class UploaderFile extends Entity
{

    /**
     * Accessible
     *
     * @var array
     */
    protected $_accessible = [
        '*' => true,
        'id' => false
    ];

    /**
     * 複数のファイルの存在チェックを行う
     *
     * @param string $fileName
     * @return    array
     */
    public function filesExists($fileName, $limited = null)
    {
        if (is_null($limited)) {
            $data = $this->find('first', ['conditions' => ['UploaderFile.name' => $fileName], 'recursive' => -1]);
            $limited = false;
            if (!empty($data['UploaderFile']['publish_begin']) || !empty($data['UploaderFile']['publish_end'])) {
                $limited = true;
            }
        }
        $pathinfo = pathinfo($fileName);
        $ext = $pathinfo['extension'];
        $basename = mb_basename($fileName, '.' . $ext);
        $files['small'] = $this->fileExists($basename . '__small' . '.' . $ext, $limited);
        $files['midium'] = $this->fileExists($basename . '__midium' . '.' . $ext, $limited);
        $files['large'] = $this->fileExists($basename . '__large' . '.' . $ext, $limited);
        return $files;
    }

    public function dummy()
    {
        foreach($dbDatas as $key => $dbData) {
            $limited = (!empty($dbData['UploaderFile']['publish_begin']) || !empty($dbData['UploaderFile']['publish_end']));
            $files = $this->UploaderFiles->filesExists($dbData['UploaderFile']['name'], $limited);
            $dbData = Set::merge($dbData, ['UploaderFile' => $files]);
            $dbDatas[$key] = $dbData;
        }
    }
}
