<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */

namespace BcSearchIndex\Model\Entity;

use Cake\I18n\FrozenTime;
use Cake\ORM\Entity as EntityAlias;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * Class SearchIndex
 * @property int $id
 * @property string $type
 * @property string $model
 * @property int $model_id
 * @property int $site_id
 * @property int $content_id
 * @property int $content_filter_id
 * @property int $lft
 * @property int $rght
 * @property string $title
 * @property string $detail
 * @property string $url
 * @property bool $status
 * @property string $priority
 * @property FrozenTime $publish_begin
 * @property FrozenTime $publish_end
 * @property FrozenTime $created
 * @property FrozenTime $modified
 */
class SearchIndex extends EntityAlias
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
