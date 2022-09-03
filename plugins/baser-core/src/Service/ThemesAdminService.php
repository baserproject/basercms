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

namespace BaserCore\Service;

use BaserCore\Utility\BcSiteConfig;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

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
        $currentThemeName = BcSiteConfig::get('theme');
        $currentTheme = null;
        foreach($themes as $key => $value) {
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
