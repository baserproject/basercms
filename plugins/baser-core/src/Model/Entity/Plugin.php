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

declare(strict_types=1);

namespace BaserCore\Model\Entity;

use Cake\Core\Configure;
use Cake\I18n\FrozenTime;
use Cake\ORM\Entity;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * Class Plugin
 * @property int $id
 * @property string $name
 * @property string|null $title
 * @property string|null $version
 * @property bool $status
 * @property bool $db_init
 * @property int $priority
 * @property FrozenTime|null $modified
 * @property FrozenTime|null $created
 */
class Plugin extends Entity
{

    /**
     * accessible
     *
     * @var array
     */
    protected $_accessible = [
        'name' => true,
        'title' => true,
        'type' => true,
        'version' => true,
        'status' => true,
        'db_init' => true,
        'priority' => true,
        'modified' => true,
        'created' => true,
        'update' => true,
        'core' => true,
        'registered' => true,
        'description' => true,
        'author' => true,
        'url' => true,
        'permission' => true,
        'installMessage' => true,
        'adminLink' => true,
        'screenshot' => true
    ];

    /**
     * プラグインかどうか判定する
     * @return bool
     * @checked
     * @noTodo
     */
    public function isPlugin(): bool
    {
        return $this->hasType(['CorePlugin', 'Plugin']);
    }

    /**
     * コアプラグインかどうか判定する
     * @return bool
     * @checked
     * @noTodo
     */
    public function isCorePlugin()
    {
        return (
            $this->hasType(['CorePlugin', 'Plugin']) &&
            in_array($this->name, Configure::read('BcApp.corePlugins'))
        );
    }

    /**
     * テーマかどうか判定する
     * @return bool
     * @checked
     * @noTodo
     */
    public function isTheme()
    {
        return $this->hasType(['Theme']);
    }

    /**
     * 管理画面用テーマかどうか判定する
     * @return bool
     * @checked
     * @noTodo
     */
    public function isAdminTheme(): bool
    {
        return $this->hasType(['AdminTheme']);
    }

    /**
     * 指定したタイプを持っているかどうか判定する
     * @param $types
     * @return bool
     * @checked
     * @noTodo
     */
    public function hasType(array|string $types): bool
    {
        $type = $this->type;
        if (!$type) return false;
        if (!is_array($type)) $type = [$type];
        foreach($type as $value) {
            if (in_array($value, $types)) return true;
        }
        return false;
    }

}
