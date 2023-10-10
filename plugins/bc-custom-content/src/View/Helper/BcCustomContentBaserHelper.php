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

namespace BcCustomContent\View\Helper;

use BaserCore\View\Helper\BcPluginBaserHelperInterface;
use Cake\View\Helper;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * MailBaserHelper
 *
 * BcBaserHelper より透過的に呼び出されるヘルパー
 */
class BcCustomContentBaserHelper extends Helper implements BcPluginBaserHelperInterface
{

    /**
     * ヘルパー
     * @var array
     */
    public $helpers = [
        'BcCustomContent.CustomContent',
    ];

    /**
     * メソッド一覧取得
     *
     * @return array[]
     * @checked
     * @noTodo
     */
    public function methods(): array
    {
        return [
            'isDisplayCustomEntrySearch' => ['CustomContent', 'isDisplayEntrySearch'],
            'customSearchControl' => ['CustomContent', 'searchControl'],
            'customContentDescription' => ['CustomContent', 'description'],
            'customEntryTitle' => ['CustomContent', 'entryTitle'],
            'customEntryPublished' => ['CustomContent', 'published'],
            'getCustomLinks' => ['CustomContent', 'getLinks'],
            'isDisplayCustomField' => ['CustomContent', 'isDisplayField'],
            'getCustomFieldTitle' => ['CustomContent', 'getFieldTitle'],
            'getCustomFieldValue' => ['CustomContent', 'getFieldValue'],
        ];
    }

}
