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

namespace BaserCore\View;

use BaserCore\View\Helper\BcFormHelper;
use BaserCore\View\Helper\BcTimeHelper;
use BaserCore\View\Helper\BcToolbarHelper;
use BaserCore\View\Helper\BcUploadHelper;
use Cake\View\View;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use BaserCore\View\Helper\BcContentsHelper;
use BaserCore\View\Helper\BcBaserHelper;
use BaserCore\Event\BcEventDispatcherTrait;

/**
 * Class AppView
 * @property BcBaserHelper $BcBaser
 * @property BcUploadHelper $BcUpload
 * @property BcToolbarHelper $BcToolbar
 * @property BcFormHelper $BcForm
 * @property BcTimeHelper $BcTime
 * @property BcContentsHelper $BcContents
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
        $this->loadHelper('BaserCore.BcTime');
        $this->loadHelper('BaserCore.BcForm', ['templates' => 'BaserCore.bc_form']);
        $this->loadHelper('BaserCore.BcAdmin');
        $this->loadHelper('BaserCore.BcContents');
        $this->loadHelper('BaserCore.BcPage');
        $this->loadHelper('BaserCore.BcBaser');
        $this->loadHelper('BaserCore.BcUpload');
        $this->loadHelper('BaserCore.BcToolbar');
        $this->loadHelper('Paginator');
        $this->assign('title', $this->get('title'));
    }

    /**
     * テンプレートを描画する
     * 固定ページで利用
     *
     * @param string $templateFile Filename of the template.
     * @return string Rendered output
     * @checked
     * @noTodo
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
     * @checked
     * @noTodo
     */
    public function getExt(): string
    {
        return $this->_ext;
    }

}
