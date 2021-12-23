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

namespace BaserCore\View;

use BaserCore\Event\BcEventDispatcherTrait;
use BaserCore\View\Helper\BcPageHelper;
use Cake\View\View;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * Class AppView
 * @package BaserCore\View
 * @property BcPageHelper $BcPage
 */
class AppView extends View
{

    /**
     * Trait
     */
    use BcEventDispatcherTrait;

    /**
     * initialize
     * @checked
     * @unitTest
     * @noTodo
     */
    public function initialize(): void
    {
        parent::initialize();
        // TODO ucmitz 未移行のため暫定措置
        // >>>
//        $this->loadHelper('BaserCore.BcHtml');
//        $this->loadHelper('BaserCore.BcForm');
//        $this->loadHelper('BaserCore.BcWidgetArea');
//        $this->loadHelper('BaserCore.BcXml');
//        $this->loadHelper('BaserCore.BcArray');
//        $this->loadHelper('BaserCore.BcXml');
//        $this->loadHelper('BaserCore.BcXml');
//        $this->loadHelper('BaserCore.BcXml');
        // <<<
        $this->loadHelper('BaserCore.BcPage');
        $this->loadHelper('BaserCore.BcBaser');
    }

    /**
     * テンプレートを描画する
     * 固定ページで利用
     *
     * @param string $templateFile Filename of the template.
     * @return string Rendered output
     */
    public function evaluate(string $templateFile): string
    {
        $dataForView = [];
        foreach($this->getVars() as $key) {
            $dataForView[$key] = $this->get($key);
        }
        return parent::_evaluate($templateFile, $dataForView);
    }

    /**
     * 拡張子を取得する
     * @return string
     */
    public function getExt(): string
    {
        return $this->_ext;
    }

}
