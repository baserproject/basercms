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

namespace BcThemeConfig\View\Helper;

use BaserCore\View\Helper\BcPluginBaserHelperInterface;
use Cake\View\Helper;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * BcThemeConfigBaserHelper
 */
class BcThemeConfigBaserHelper extends Helper implements BcPluginBaserHelperInterface
{

    /**
     * Helper
     *
     * @var string[]
     */
    public $helpers = ['BcThemeConfig.BcThemeConfig'];

    /**
     * メソッド一覧取得
     *
     * @return array[]
     */
    public function methods(): array
    {
        return [
            'logo' => ['BcThemeConfig', 'logo'],
            'mainImage' => ['BcThemeConfig', 'mainImage']
        ];
    }

}
