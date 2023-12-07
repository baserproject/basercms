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

namespace BaserCore\Test\Scenario;

use BaserCore\Test\Factory\SiteFactory;
use CakephpFixtureFactories\Scenario\FixtureScenarioInterface;

/**
 * SiteBcContentsRouteScenario
 *
 * 利用する場合は、テーブルの初期化に次のフィクスチャの定義が必要
 */
class SiteBcContentsRouteScenario implements FixtureScenarioInterface
{

    /**
     * load
     */
    public function load(...$args)
    {
        SiteFactory::make([
            'id' => '1',
            'main_site_id' => null,
            'name' => '',
            'display_name' => 'メイン',
            'title' => 'メイン',
            'alias' => '',
            'theme' => 'BcFront',
            'status' => 1,
            'use_subdomain' => 0,
            'relate_main_site' => 0,
            'device' => 'mobile',
            'lang' => '',
            'same_main_url' => 0,
            'auto_redirect' => 0,
            'auto_link' => 0,
            'domain_type' => 0,
            'created' => '2016-11-04 16:33:52',
            'modified' => '2016-11-04 16:39:36'
        ])->persist();
        SiteFactory::make([
            'id' => '2',
            'main_site_id' => '0',
            'name' => 'smartphone',
            'display_name' => 'スマートフォン',
            'title' => 'baserCMS inc.｜スマホ',
            'alias' => 's',
            'theme' => '',
            'status' => 1,
            'use_subdomain' => 0,
            'relate_main_site' => 1,
            'device' => 'smartphone',
            'lang' => '',
            'same_main_url' => 1,
            'auto_redirect' => 0,
            'auto_link' => 0,
            'domain_type' => 0,
            'created' => '2016-11-04 16:33:52',
            'modified' => '2016-11-04 16:42:25'
        ])->persist();
        SiteFactory::make([
            'id' => '3',
            'main_site_id' => '0',
            'name' => 'english',
            'display_name' => 'English',
            'title' => 'baserCMS inc.｜English',
            'alias' => 'en',
            'theme' => '',
            'status' => 1,
            'use_subdomain' => 0,
            'relate_main_site' => 0,
            'device' => '',
            'lang' => '',
            'same_main_url' => 0,
            'auto_redirect' => 0,
            'auto_link' => 0,
            'domain_type' => 0,
            'created' => '2016-11-04 16:35:39',
            'modified' => '2016-11-04 16:40:34'
        ])->persist();
        SiteFactory::make([
            'id' => '4',
            'main_site_id' => '0',
            'name' => 'subdomain',
            'display_name' => 'サブドメイン',
            'title' => 'baserCMS inc.｜サブドメイン',
            'alias' => 'sub',
            'theme' => '',
            'status' => 1,
            'use_subdomain' => 1,
            'relate_main_site' => 0,
            'device' => '',
            'lang' => '',
            'same_main_url' => 0,
            'auto_redirect' => 0,
            'auto_link' => 0,
            'domain_type' => 1,
            'created' => '2016-11-04 16:36:38',
            'modified' => '2016-11-04 16:41:14'
        ])->persist();
        SiteFactory::make([
            'id' => '5',
            'main_site_id' => '0',
            'name' => 'another',
            'display_name' => '別ドメイン',
            'title' => 'baserCMS inc.｜別ドメイン',
            'alias' => 'another.com',
            'theme' => '',
            'status' => 1,
            'use_subdomain' => 1,
            'relate_main_site' => 0,
            'device' => '',
            'lang' => '',
            'same_main_url' => 0,
            'auto_redirect' => 0,
            'auto_link' => 0,
            'domain_type' => 2,
            'created' => '2016-11-04 16:38:36',
            'modified' => '2016-11-04 16:42:01'
        ])->persist();
        SiteFactory::make([
            'id' => '6',
            'main_site_id' => '5',
            'name' => 'another_smartphone',
            'display_name' => '別ドメインスマートフォン',
            'title' => 'baserCMS inc.｜別ドメインスマートフォン',
            'alias' => 'another.com/s',
            'theme' => '',
            'status' => 1,
            'use_subdomain' => 1,
            'relate_main_site' => 0,
            'device' => 'smartphone',
            'lang' => '',
            'same_main_url' => 1,
            'auto_redirect' => 0,
            'auto_link' => 0,
            'domain_type' => 2,
            'created' => '2016-11-04 19:41:47',
            'modified' => '2016-11-04 19:41:47'
        ])->persist();
    }

}
