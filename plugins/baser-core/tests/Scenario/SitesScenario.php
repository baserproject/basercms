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
 * Site
 *
 */
class SitesScenario implements FixtureScenarioInterface
{

    /**
     * load
     */
    public function load(...$args): mixed
    {
        SiteFactory::make(
            [
                'id' => '1',
                'main_site_id' => null,
                'name' => '',
                'display_name' => 'メインサイト',
                'title' => 'baserCMS inc.',
                'alias' => '',
                'theme' => 'BcFront',
                'status' => true,
                'use_subdomain' => false,
                'relate_main_site' => false,
                'device' => '',
                'lang' => '',
                'same_main_url' => false,
                'auto_redirect' => false,
                'auto_link' => false,
                'domain_type' => null,
                'created' => '2021-07-01 21:20:15',
                'modified' => null
            ]
        )->persist();
        SiteFactory::make(
            [
                'id' => '2',
                'main_site_id' => 1,
                'name' => 'smartphone',
                'display_name' => 'スマホサイト',
                'title' => 'baserCMS inc.｜スマホ',
                'alias' => 's',
                'theme' => '',
                'status' => false,
                'use_subdomain' => false,
                'relate_main_site' => true,
                'device' => 'smartphone',
                'lang' => '',
                'same_main_url' => false,
                'auto_redirect' => true,
                'auto_link' => true,
                'domain_type' => null,
                'created' => '2021-07-01 21:20:15',
                'modified' => null
            ])->persist();
        SiteFactory::make(
            [
                'id' => '3',
                'main_site_id' => 1,
                'name' => 'en',
                'display_name' => '英語サイト',
                'title' => 'baserCMS inc.｜English',
                'alias' => 'en',
                'theme' => '',
                'status' => true,
                'use_subdomain' => false,
                'relate_main_site' => false,
                'device' => '',
                'lang' => 'english',
                'same_main_url' => false,
                'auto_redirect' => true,
                'auto_link' => false,
                'domain_type' => null,
                'created' => '2021-07-01 21:20:15',
                'modified' => null
            ])->persist();
        SiteFactory::make(
            [
                'id' => '4',
                'main_site_id' => 1,
                'name' => 'en',
                'display_name' => '別ドメイン',
                'title' => 'baserCMS inc.｜English',
                'alias' => 'basercms.net',
                'theme' => '',
                'status' => true,
                'use_subdomain' => true,
                'relate_main_site' => false,
                'device' => '',
                'lang' => '',
                'same_main_url' => false,
                'auto_redirect' => true,
                'auto_link' => false,
                'domain_type' => 2,
                'created' => '2021-07-01 21:20:15',
                'modified' => null
            ])->persist();
        SiteFactory::make(
            [
                'id' => '5',
                'main_site_id' => 1,
                'name' => 'en',
                'display_name' => 'サブドメイン',
                'title' => 'baserCMS inc.｜English',
                'alias' => 'sub',
                'theme' => '',
                'status' => true,
                'use_subdomain' => true,
                'relate_main_site' => false,
                'device' => '',
                'lang' => '',
                'same_main_url' => false,
                'auto_redirect' => true,
                'auto_link' => false,
                'domain_type' => 1,
                'created' => '2021-07-01 21:20:15',
                'modified' => null
            ])->persist();
        SiteFactory::make(
            [
                'id' => '6',
                'main_site_id' => 1,
                'name' => 'related',
                'display_name' => '関連メインサイト用',
                'title' => 'baserCMS inc.｜Related',
                'alias' => '',
                'theme' => '',
                'status' => true,
                'use_subdomain' => true,
                'relate_main_site' => true,
                'device' => '',
                'lang' => '',
                'same_main_url' => false,
                'auto_redirect' => true,
                'auto_link' => false,
                'domain_type' => 1,
                'created' => '2021-07-01 21:20:15',
                'modified' => null
            ])->persist();
        return null;
    }

}
