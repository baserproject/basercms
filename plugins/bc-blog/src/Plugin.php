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
use BaserCore\Model\Table\SitesTable;
use BaserCore\Utility\BcContainerTrait;
use BcBlog\ServiceProvider\BcBlogServiceProvider;
use Cake\Core\ContainerInterface;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use Cake\Http\ServerRequestFactory;
use Cake\ORM\TableRegistry;

/**
 * plugin for BcBlog
 */
class Plugin extends BcPlugin
{

    /**
     * Trait
     */
    use BcContainerTrait;

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
        $result = parent::install($options);
        // ブログ記事の投稿日を更新
        $this->updateDateNow('BcBlog.BlogPosts', ['posted']);
        return $result;
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
     * @unitTest
     */
    public function services(ContainerInterface $container): void
    {
        $container->addServiceProvider(new BcBlogServiceProvider());
    }

    /**
     * routes
     * @param \Cake\Routing\RouteBuilder $routes
     * @checked
     * @noTodo
     * @unitTest
     */
    public function routes($routes): void
    {
        /**
         * Tag
         */
        $routes->connect('/tags/*', [
            'plugin' => 'BcBlog',
            'controller' =>
            'blog',
            'action' => 'tags'
        ]);

        $request = ServerRequestFactory::fromGlobals();
        /* @var SitesTable $sitesTable */
        $sitesTable = TableRegistry::getTableLocator()->get('BaserCore.Sites');
        $site = $sitesTable->findByUrl($request->getPath());
        if ($site) {
            $routes->connect("/{$site->alias}/tags/*", [
                'sitePrefix' => $site->name,
                'plugin' => 'BcBlog',
                'controller' => 'blog',
                'action' => 'tags'
            ]);
        }

        parent::routes($routes);
    }

}
