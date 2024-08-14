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
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;

/**
 * BaserCoreBaserHelper
 *
 * BcBaserHelper より透過的に呼び出されるメソッドを配置するヘルパー
 *
 * @property BcContentsHelper $BcContents
 */
#[\AllowDynamicProperties]
class BaserCoreBaserHelper extends Helper implements BcPluginBaserHelperInterface
{
    /**
     * ヘルパー
     *
     * @var string[]
     */
    public array $helpers = [
        'BaserCore.BcContents',
        'BaserCore.BcForm',
        'BaserCore.BcUpload',
        'Html',
        'Text'
    ];

    /**
     * BcBaserHelper のメソッドと、外部ヘルパーの関連付けを返却する
     * @return array[]
     * @checked
     * @noTodo
     * @unitTest
     */
    public function methods(): array
    {
        return [
            'getParentContent' => ['BcContents', 'getParent'],
            'createForm' => ['BcForm', 'create'],
            'formControl' => ['BcForm', 'control'],
            'formHidden' => ['BcForm', 'hidden'],
            'formSubmit' => ['BcForm', 'submit'],
            'formError' => ['BcForm', 'error'],
            'formLabel' => ['BcForm', 'label'],
            'endForm' => ['BcForm', 'end'],
            'scriptStart' => ['Html', 'scriptStart'],
            'scriptEnd' => ['Html', 'scriptEnd'],
            'meta' => ['Html', 'meta'],
            'setTableToUpload' => ['BcUpload', 'setTable'],
            'truncateText' => ['Text', 'truncate'],
            'getCurrentSite' => ['BcContents', 'getCurrentSite'],
            'getCurrentContent' => ['BcContents', 'getCurrentContent']
        ];
    }

}
