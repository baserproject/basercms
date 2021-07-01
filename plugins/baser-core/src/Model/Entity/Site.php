<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */

namespace BaserCore\Model\Entity;

use Cake\I18n\Time as TimeAlias;
use Cake\ORM\Entity as EntityAlias;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * Class Site
 * @package BaserCore\Model\Entity
 * @property int $id
 * @property string $name
 * @property int $main_site_id
 * @property string $display_name
 * @property string $title
 * @property string $alias
 * @property string $theme
 * @property bool $status
 * @property string $keyword
 * @property string $description
 * @property bool $relate_main_site
 * @property string $device
 * @property string $lang
 * @property bool $same_main_url
 * @property bool $auto_redirect
 * @property bool $auto_link
 * @property bool $use_subdomain
 * @property int $domain_type
 * @property TimeAlias $created
 * @property TimeAlias $modified
 */
class Site extends EntityAlias
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

}
