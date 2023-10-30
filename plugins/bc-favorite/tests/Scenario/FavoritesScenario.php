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

namespace BcFavorite\Test\Scenario;

use BcFavorite\Test\Factory\FavoriteFactory;
use CakephpFixtureFactories\Scenario\FixtureScenarioInterface;

/**
 * FavoritesScenario
 */
class FavoritesScenario implements FixtureScenarioInterface
{

    /**
     * load
     */
    public function load(...$args)
    {
        FavoriteFactory::make([
            'id' => 1,
            'name' => '固定ページ管理',
            'url' => '/admin/pages/index',
            'sort' => 1,
        ])->persist();
        FavoriteFactory::make([
            'name' => '新着情報管理',
            'url' => '/admin/blog/blog_posts/index/1',
            'sort' => 2,
            'created' => '2015-01-27 12:57:59',
            'modified' => '2015-01-27 12:57:59'
        ])->persist();
        FavoriteFactory::make([
            'name' => 'お問い合わせ管理',
            'url' => '/admin/mail/mail_fields/index/1',
            'sort' => 3,
        ])->persist();
        FavoriteFactory::make([
            'name' => '受信メール一覧',
            'url' => '/admin/mail/mail_messages/index/1',
            'sort' => 4,
        ])->persist();
        FavoriteFactory::make([
            'name' => 'コメント一覧',
            'url' => '/admin/blog/blog_comments/index/1',
            'sort' => 5,
        ])->persist();
        FavoriteFactory::make([
            'name' => 'クレジット',
            'url' => 'javascript:credit();',
            'sort' => 6,
        ])->persist();
    }
}
