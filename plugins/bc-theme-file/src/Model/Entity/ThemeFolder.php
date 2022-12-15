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

/**
 * ThemeFolder
 *
 * @property string $name
 * @property string $path
 * @protected string $parent
 * @protected string $fullpath
 * @protected string $type
 */
class ThemeFolder extends \Cake\ORM\Entity
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
        'path',
        'parent'
    ];

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
        } else {
            $properties['parent'] = dirname($properties['fullpath']) . DS;
        }
           $properties['fullpath'] = preg_replace('/\/$/', '', $properties['fullpath']);
        parent::__construct($properties, $options);
    }

    /**
     * フォルダ名を取得する
     *
     * @return array|mixed|string|string[]
     */
    protected function _getName()
    {
        return str_replace($this->parent, '', $this->fullpath);
    }

}
