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

use BaserCore\Test\Factory\SiteConfigFactory;
use CakephpFixtureFactories\Scenario\FixtureScenarioInterface;

/**
 * SiteConfig
 *
 */
class SiteConfigsScenario implements FixtureScenarioInterface
{

    /**
     * load
     */
    public function load(...$args): mixed
    {
        SiteConfigFactory::make([
                'id' => '1',
                'name' => 'address',
                'value' => '福岡県',
                'created' => '2021-01-27 12:56:52',
                'modified' => '2021-01-27 13:01:58'
            ]
        )->persist();
        SiteConfigFactory::make([
                'id' => '2',
                'name' => 'theme',
                'value' => 'nada-icons',
                'created' => '2021-01-27 12:56:52',
                'modified' => '2021-01-27 12:58:25'
            ]
        )->persist();
        SiteConfigFactory::make([
                'id' => '3',
                'name' => 'email',
                'value' => 'basertest@example.com',
                'created' => '2021-01-27 12:56:52',
                'modified' => '2021-01-27 13:01:58'
            ]
        )->persist();
        SiteConfigFactory::make([
                'id' => '4',
                'name' => 'widget_area',
                'value' => '1',
                'created' => '2021-01-27 12:56:52',
                'modified' => '2021-01-27 13:01:58'
            ]
        )->persist();
        SiteConfigFactory::make([
                'id' => '5',
                'name' => 'maintenance',
                'value' => '0',
                'created' => '2021-01-27 12:56:52',
                'modified' => '2021-01-27 13:01:58'
            ]
        )->persist();
        SiteConfigFactory::make([
                'id' => '7',
                'name' => 'smtp_host',
                'value' => '',
                'created' => '2021-01-27 12:56:52',
                'modified' => '2021-01-27 13:01:58'
            ]
        )->persist();
        SiteConfigFactory::make([
                'id' => '8',
                'name' => 'smtp_user',
                'value' => '',
                'created' => '2021-01-27 12:56:52',
                'modified' => '2021-01-27 13:01:58'
            ]
        )->persist();
        SiteConfigFactory::make([
                'id' => '9',
                'name' => 'smtp_password',
                'value' => '',
                'created' => '2021-01-27 12:56:52',
                'modified' => '2021-01-27 13:01:58'
            ]
        )->persist();
        SiteConfigFactory::make([
                'id' => '10',
                'name' => 'smtp_port',
                'value' => '',
                'created' => '2021-01-27 12:56:52',
                'modified' => '2021-01-27 13:01:58'
            ]
        )->persist();
        SiteConfigFactory::make([
                'id' => '11',
                'name' => 'admin_list_num',
                'value' => '10',
                'created' => '2021-01-27 12:56:52',
                'modified' => '2021-01-27 13:01:58'
            ]
        )->persist();
        SiteConfigFactory::make([
                'id' => '12',
                'name' => 'google_analytics_id',
                'value' => '',
                'created' => '2021-01-27 12:56:52',
                'modified' => '2021-01-27 13:01:58'
            ]
        )->persist();
        SiteConfigFactory::make([
                'id' => '14',
                'name' => 'content_types',
                'value' => 'YTozOntzOjk6IuODluODreOCsCI7czo5OiLjg5bjg63jgrAiO3M6OToi44Oa44O844K4IjtzOjk6IuODmuODvOOCuCI7czo5OiLjg6Hjg7zjg6siO3M6OToi44Oh44O844OrIjt9',
                'created' => '2021-01-27 12:56:52',
                'modified' => '2021-01-27 12:58:25'
            ]
        )->persist();
        SiteConfigFactory::make([
                'id' => '16',
                'name' => 'admin_theme',
                'value' => 'BcAdminThird',
                'created' => '2021-01-27 12:56:52',
                'modified' => '2021-01-27 12:58:25'
            ]
        )->persist();
        SiteConfigFactory::make([
                'id' => '17',
                'name' => 'login_credit',
                'value' => '1',
                'created' => '2021-01-27 12:56:52',
                'modified' => '2021-01-27 13:01:58'
            ]
        )->persist();
        SiteConfigFactory::make([
                'id' => '18',
                'name' => 'first_access',
                'value' => '',
                'created' => '2021-01-27 12:56:52',
                'modified' => '2021-01-27 13:01:32'
            ]
        )->persist();
        SiteConfigFactory::make([
                'id' => '19',
                'name' => 'editor',
                'value' => 'BcCkeditor',
                'created' => '2021-01-27 12:56:52',
                'modified' => '2021-01-27 13:01:58'
            ]
        )->persist();
        SiteConfigFactory::make([
                'id' => '20',
                'name' => 'editor_styles',
                'value' => '#青見出し
h3 {
color:Blue;
}
#赤見出し
h3 {
color:Red;
}
#黄マーカー
span {
background-color:Yellow;
}
#緑マーカー
span {
background-color:Lime;
}
#大文字
big {}
#小文字
small {}
#コード
code {}
#削除文
del {}
#挿入文
ins {}
#引用
cite {}
#インライン
q {}',
                'created' => '2021-01-27 12:56:52',
                'modified' => '2021-01-27 13:01:58'
            ]
        )->persist();
        SiteConfigFactory::make([
                'id' => '21',
                'name' => 'editor_enter_br',
                'value' => '0',
                'created' => '2021-01-27 12:56:52',
                'modified' => '2021-01-27 13:01:58'
            ]
        )->persist();
        SiteConfigFactory::make([
                'id' => '22',
                'name' => 'admin_side_banner',
                'value' => '1',
                'created' => '2021-01-27 12:56:53',
                'modified' => '2021-01-27 13:01:58'
            ]
        )->persist();
        SiteConfigFactory::make([
                'id' => '23',
                'name' => 'smtp_tls',
                'value' => '0',
                'created' => '2021-01-27 12:56:53',
                'modified' => '2021-01-27 13:01:58'
            ]
        )->persist();
        SiteConfigFactory::make([
                'id' => '24',
                'name' => 'version',
                'value' => '3.0.6.1',
                'created' => '2021-01-27 12:58:10',
                'modified' => '2021-01-27 12:58:25'
            ]
        )->persist();
        SiteConfigFactory::make([
                'id' => '26',
                'name' => 'google_maps_api_key',
                'value' => '',
                'created' => '2021-01-27 12:58:10',
                'modified' => '2021-01-27 12:58:25'
            ]
        )->persist();
        SiteConfigFactory::make([
                'id' => '28',
                'name' => 'use_site_device_setting',
                'value' => '1',
                'created' => '2021-01-27 12:58:10',
                'modified' => '2021-01-27 12:58:25'
            ]
        )->persist();
        SiteConfigFactory::make([
                'id' => '29',
                'name' => 'use_site_lang_setting',
                'value' => '0',
                'created' => '2021-01-27 12:58:10',
                'modified' => '2021-01-27 12:58:25'
            ]
        )->persist();
        SiteConfigFactory::make([
                'id' => '31',
                'name' => 'editor',
                'value' => 'BcCkeditor',
                'created' => '2021-01-27 12:58:10',
                'modified' => '2021-01-27 12:58:25'
            ]
        )->persist();
        return null;
    }

}
