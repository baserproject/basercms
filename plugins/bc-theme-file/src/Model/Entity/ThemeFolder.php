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

use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

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
    protected array $_accessible = [
        '*' => true,
        'id' => false
    ];

    /**
     * Virtual
     *
     * @var string[]
     */
    protected array $_virtual = [
        'name',
        'path',
        'parent'
    ];

    /**
     * Constructor
     *
     * @param array $properties
     * @param array $options
     * @checked
     * @noTodo
     * @unitTest
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
     * @checked
     * @noTodo
     * @unitTest
     */
    protected function _getName()
    {
        if($this->fullpath . '/' === $this->parent) return '';
        return str_replace($this->parent, '', $this->fullpath);
    }

}
