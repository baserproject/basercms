<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.2.0
 * @license       https://basercms.net/license/index.html MIT License
 */

namespace BcSeo\Model\Entity;

use Cake\ORM\Entity;

/**
 * Class SeoMeta
 */
class SeoMeta extends Entity
{
    protected array $_accessible = [
        // コンテンツ複製時にSeoMetaも複製されるようにする
        'id' => false,
        '*' => true,
    ];
}
