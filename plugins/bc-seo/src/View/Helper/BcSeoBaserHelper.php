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

namespace BcSeo\View\Helper;

use BaserCore\View\Helper\BcPluginBaserHelperInterface;
use Cake\View\Helper;

/**
 * Class BcSeoBaserHelper
 *
 * BcBaserHelper より透過的に呼び出される
 */
class BcSeoBaserHelper extends Helper implements BcPluginBaserHelperInterface
{
    public array $helpers = [
        'BcSeo.Seo'
    ];

    /**
     * メソッド一覧取得
     */
    public function methods(): array
    {
        return [
            'seoMeta' => ['Seo', 'meta'],
            'getSeoMeta' => ['Seo', 'getMeta'],
        ];
    }
}
