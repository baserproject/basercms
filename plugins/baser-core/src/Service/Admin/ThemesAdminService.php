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

namespace BaserCore\Service\Admin;

use BaserCore\Service\ThemesService;
use BaserCore\Utility\BcUtil;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use Cake\Core\Configure;
use Cake\Utility\Inflector;

/**
 * ThemesAdminService
 */
class ThemesAdminService extends ThemesService implements ThemesAdminServiceInterface
{

    /**
     * 一覧画面用のデータを取得する
     * @param array $themes
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getViewVarsForIndex($themes): array
    {
        $currentThemeName = BcUtil::getCurrentTheme();
        $currentTheme = null;
        foreach($themes as $key => $value) {
            if($value['name'] === Inflector::camelize(Configure::read('BcApp.coreFrontTheme'), '-')) {
                unset($themes[$key]);
            }
            if ($value['name'] === $currentThemeName) {
                $currentTheme = $value;
                unset($themes[$key]);
            }
        }
        return [
            'themes' => $themes,
            'currentTheme' => $currentTheme,
            'defaultDataPatterns' => $this->getDefaultDataPatterns($currentThemeName, ['useTitle' => false])
        ];
    }

}
