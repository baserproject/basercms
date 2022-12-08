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

namespace BcWidgetArea\View\Helper;

use Cake\View\Helper;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * BcWidgetAreaBaserHelper
 *
 * @property BcWidgetAreaHelper $BcWidgetArea
 */
class BcWidgetAreaBaserHelper extends Helper
{

    /**
     * Helpers
     * @var string[]
     */
    public $helpers = ['BcWidgetArea.BcWidgetArea'];

    /**
     * ウィジェットエリアを表示する
     *
     * @param null $no
     * @param array $options
     * @checked
     * @noTodo
     * @unitTest ラッパーメソッドのためユニットテストは実装しない
     */
    public function widgetArea($no = null, $options = [])
    {
        $this->BcWidgetArea->widgetArea($no, $options);
    }

    /**
     * ウィジェットエリアを取得する
     *
     * @param null $no
     * @param array $options
     * @return string
     * @checked
     * @noTodo
     * @unitTest ラッパーメソッドのためユニットテストは実装しない
     */
    public function getWidgetArea($no = null, $options = [])
    {
        return $this->BcWidgetArea->getWidgetArea($no, $options);
    }

}
