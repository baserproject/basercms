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

use BaserCore\Utility\BcUtil;
use Cake\I18n\FrozenTime;
use Cake\ORM\Entity;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use Cake\ORM\TableRegistry;

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
     * @return array
     * @checked
     * @noTodo
     */
    public function filesExists()
    {
        return [
            'small' => $this->small,
            'midium' => $this->midium,
            'large' => $this->large
        ];
    }

    /**
     * get small
     *
     * @return bool
     * @checked
     * @noTodo
     */
    protected function _getSmall()
    {
        return $this->fileExists($this->getFileNameBySize('small'));
    }

    /**
     * get midium
     *
     * @return bool
     * @checked
     * @noTodo
     */
    protected function _getMidium()
    {
        return $this->fileExists($this->getFileNameBySize('midium'));
    }

    /**
     * get large
     *
     * @return bool
     * @checked
     * @noTodo
     */
    protected function _getLarge()
    {
        return $this->fileExists($this->getFileNameBySize('large'));
    }

    /**
     * 各サイズのファイル名を取得する
     *
     * @param string $size
     * @return string
     * @checked
     * @noTodo
     */
    private function getFileNameBySize(string $size): string
    {
        $pathinfo = pathinfo($this->name);
        $ext = $pathinfo['extension'];
        $basename = BcUtil::mbBasename($this->name, '.' . $ext);
        return $basename . '__' . $size . '.' . $ext;
    }

    /**
     * ファイルの存在チェックを行う
     *
     * @param string $fileName
     * @return bool
     * @checked
     * @noTodo
     */
    public function fileExists($fileName)
    {
        $uploaderFilesTable = TableRegistry::getTableLocator()->get('BcUploader.UploaderFiles');
        $settings = $uploaderFilesTable->getSettings();
        if ($this->isLimited()) {
            $savePath = WWW_ROOT . 'files' . DS . $settings['saveDir'] . DS . 'limited' . DS . $fileName;
        } else {
            $savePath = WWW_ROOT . 'files' . DS . $settings['saveDir'] . DS . $fileName;
        }
        return file_exists($savePath);
    }

    /**
     * 閲覧制限が設定されているかどうか
     *
     * @return bool
     * @checked
     * @noTodo
     */
    public function isLimited()
    {
        return ($this->publish_begin || $this->publish_end);
    }

}
