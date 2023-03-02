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

use BaserCore\Utility\BcUtil;
use BaserCore\View\Helper\BcTextHelper;
use Cake\Core\Configure;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\Note;
use Cake\Core\Plugin;
use Cake\Filesystem\Folder;
use Cake\Utility\Inflector;

/**
 * BcFrontAppView
 * @uses BcFrontAppView
 * @property BcTextHelper $BcText
 */
class BcFrontAppView extends AppView
{

    /**
     * initialize
     * @checked
     * @unitTest
     * @noTodo
     */
    public function initialize(): void
    {
        parent::initialize();
        if (!empty($this->getRequest()->getAttribute('currentSite')->device)) {
            $agentHelper = Configure::read('BcAgent.' . $this->getRequest()->getAttribute('currentSite')->device . '.helper');
            if ($agentHelper) $this->loadHelper($agentHelper);
        }
        $this->loadHelper('BaserCore.BcText');
        if (BcUtil::isInstalled()) {
            $this->setThemeHelpers();
        }
    }

    /**
     * テーマ用のヘルパーをセットする
     *
     * @return void
     */
    protected function setThemeHelpers(): void
    {
        $theme = BcUtil::getCurrentTheme();
        $themeHelpersPath = Plugin::path($theme) . 'src' . DS . 'View' . DS . 'Helper';
        $Folder = new Folder($themeHelpersPath);
        $files = $Folder->read(true, true);
        if (empty($files[1])) return;

        foreach($files[1] as $file) {
            $this->loadHelper(Inflector::camelize($theme, '-') . '.' . basename($file, 'Helper.php'));
        }
    }

}
