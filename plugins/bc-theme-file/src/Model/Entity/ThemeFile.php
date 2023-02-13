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

namespace BcThemeFile\Model\Entity;

use Cake\Filesystem\File;

/**
 * ThemeFile
 *
 * @property string $fullpath
 * @property string $name
 * @property string $base_name
 * @property string $ext
 * @property string $type
 * @property string $path
 * @property string $parent
 * @property string $contents
 */
class ThemeFile extends \Cake\ORM\Entity
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
     * Virtual
     *
     * @var string[]
     */
    protected $_virtual = [
        'name',
        'base_name',
        'ext',
        'type',
        'path',
        'parent',
        'contents'
    ];

    /**
     * 新規作成時の想定拡張子
     *
     * 新規登録画面に表示するのに利用する
     *
     * @var mixed|string|null
     */
    protected $_newExt = null;

    /**
     * Constructor
     *
     * @param array $properties
     * @param array $options
     */
    public function __construct(array $properties = [], array $options = [])
    {
        if (!empty($options['new'])) {
            $properties['parent'] = $properties['fullpath'];
            if (!empty($options['type'])) {
                if ($options['type'] === 'css' || $options['type'] === 'js') {
                    $this->_newExt = $options['type'];
                } else {
                    $this->_newExt = 'php';
                }
            }
        } else {
            $properties['parent'] = dirname($properties['fullpath']) . DS;
        }
        parent::__construct($properties, $options);
    }

    /**
     * ファイルタイプを取得する
     *
     * @return string
     */
    protected function _getType()
    {
        if (preg_match('/^(.+?)(\.ctp|\.php|\.css|\.js)$/is', $this->name)) return 'text';
        if (preg_match('/^(.+?)(\.png|\.gif|\.jpg|\.jpeg)$/is', $this->name)) return 'image';
        return 'file';
    }

    /**
     * ファイル名を取得する
     *
     * @return string
     */
    protected function _getName()
    {
        return rawurldecode(basename($this->fullpath));
    }

    /**
     * 拡張子無しのファイル名を取得する
     *
     * @return string
     */
    protected function _getBaseName()
    {
        if ($this->isNew()) {
            return '';
        } else {
            $pathinfo = pathinfo($this->fullpath);
            return rawurldecode(basename($this->fullpath, '.' . $pathinfo['extension']));
        }
    }

    /**
     * 拡張子を取得する
     *
     * @return mixed|string|null
     */
    protected function _getExt()
    {
        if ($this->isNew()) {
            return $this->_newExt;
        } else {
            $pathinfo = pathinfo($this->fullpath);
            return $pathinfo['extension'];
        }
    }

    /**
     * ファイルの内容を取得する
     *
     * タイプが text の場合のみ
     *
     * @return false|string
     */
    protected function _getContents()
    {
        if ($this->type === 'text') {
            if(file_exists($this->fullpath)) {
                $file = new File($this->fullpath);
                return $file->read();
            }
        }
        return '';
    }

    /**
     * 新規作成モードが確認する
     *
     * @return bool
     */
    public function isNew(): bool
    {
        return ($this->fullpath === $this->parent);
    }

}
