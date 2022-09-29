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

namespace BcBlog;

use BaserCore\BcPlugin;
use BcBlog\ServiceProvider\BcBlogServiceProvider;
use Cake\Core\ContainerInterface;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;

/**
 * plugin for ContactManager
 */
class Plugin extends BcPlugin
{

    /**
     * プラグインをインストールする
     *
     * @param array $options
     *  - `plugin` : プラグイン名
     *  - `connection` : コネクション名
     * @noTodo
     * @checked
     * @unitTest 利用例として継承しているだけのためユニットテストはスキップ
     */
    public function install($options = []) : bool
    {
        // ここに必要なインストール処理を記述
        return parent::install($options);
    }

    /**
     * プラグインをアンインストールする
     *
     * @param array $options
     *  - `plugin` : プラグイン名
     *  - `connection` : コネクション名
     *  - `target` : ロールバック対象バージョン
     * @noTodo
     * @checked
     * @unitTest 利用例として継承しているだけのためユニットテストはスキップ
     */
    public function uninstall($options = []): bool
    {
        // ここに必要なアンインストール処理を記述
        return parent::uninstall($options);
    }

    /**
     * services
     * @param ContainerInterface $container
     * @noTodo
     * @checked
     */
    public function services(ContainerInterface $container): void
    {
        $container->addServiceProvider(new BcBlogServiceProvider());
    }

}
