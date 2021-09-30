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

declare(strict_types=1);

namespace BaserCore\Model\Entity;

use Cake\ORM\Entity;

/**
 * ContentFolder
 */
class ContentFolder extends Entity
{

    /**
     * accessible
     *
     * @var array
     */
    protected $_accessible = [
         '*' => true,
        'id' => false
    ];

}
