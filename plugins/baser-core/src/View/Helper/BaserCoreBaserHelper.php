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

namespace BaserCore\View\Helper;

use Cake\View\Helper;

/**
 * BaserCoreBaserHelper
 *
 * BcBaserHelper より透過的に呼び出されるメソッドを配置するヘルパー
 *
 * @property BcContentsHelper $BcContents
 */
class BaserCoreBaserHelper extends Helper implements BcPluginBaserHelperInterface
{
    /**
     * ヘルパー
     *
     * @var string[]
     */
    public $helpers = [
        'BaserCore.BcContents'
    ];

    public function methods(): array
    {
        return [
            'getParentContent' => ['BcContents', 'getParent'],
            'widgetArea' => ['BcWidgetArea', 'widgetArea']
        ];
    }

}
