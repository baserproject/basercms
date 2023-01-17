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

/**
 * Class Plugin
 * @package BaserCore\Model\Entity
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

    public function isPlugin()
    {
        return $this->hasType(['CorePlugin', 'Plugin']);
    }

    public function isCorePlugin()
    {
        return (
            $this->hasType(['CorePlugin', 'Plugin'] &&
            in_array($this->name, Configure::read('BcApp.corePlugins')))
        );
    }

    public function isTheme()
    {
        return $this->hasType(['Theme']);
    }

    public function isAdminTheme()
    {
        return $this->hasType(['AdminTheme']);
    }

    public function hasType($types)
    {
        $type = $this->type;
        if(!$type) return false;
        if(!is_array($type)) $type = [$type];
        foreach($type as $value) {
            if(in_array($value, $types)) return true;
        }
        return false;
    }

}
