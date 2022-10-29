<?php
declare(strict_types=1);
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */

namespace BcMail;

use BaserCore\BcPlugin;

/**
 * plugin for BcMail
 */
class Plugin extends BcPlugin
{
    /**
     * プラグインをインストールする
     *
     * @param array $options
     *  - `plugin` : プラグイン名
     *  - `connection` : コネクション名
     */
    public function install($options = []) : bool
    {
        // ここに必要なインストール処理を記述
        return parent::install($options);
    }

    /**
     * プラグインをアンインストールする
     *  - `plugin` : プラグイン名
     *  - `connection` : コネクション名
     *  - `target` : ロールバック対象バージョン
     */
    public function uninstall($options = []): bool
    {
        // ここに必要なアンインストール処理を記述
        return parent::uninstall();
    }

}
