<?php
declare(strict_types=1);

use Migrations\AbstractSeed;

/**
 * SiteConfigs seed.
 */
class SiteConfigsSeed extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeds is available here:
     * https://book.cakephp.org/phinx/0/en/seeding.html
     *
     * @return void
     */
    public function run()
    {
        $data = [
            [
                'id' => '1',
                'name' => 'address',
                'value' => '福岡県',
                'created' => '2021-01-27 12:56:52',
                'modified' => '2021-01-27 13:01:58'
            ],
            [
                'id' => '2',
                'name' => 'theme',
                'value' => 'nada-icons',
                'created' => '2021-01-27 12:56:52',
                'modified' => '2021-01-27 12:58:25'
            ],
            [
                'id' => '3',
                'name' => 'email',
                'value' => 'basertest@example.com',
                'created' => '2021-01-27 12:56:52',
                'modified' => '2021-01-27 13:01:58'
            ],
            [
                'id' => '4',
                'name' => 'widget_area',
                'value' => '1',
                'created' => '2021-01-27 12:56:52',
                'modified' => '2021-01-27 13:01:58'
            ],
            [
                'id' => '5',
                'name' => 'maintenance',
                'value' => '0',
                'created' => '2021-01-27 12:56:52',
                'modified' => '2021-01-27 13:01:58'
            ],
            [
                'id' => '6',
                'name' => 'mail_encode',
                'value' => 'UTF-8',
                'created' => '2021-01-27 12:56:52',
                'modified' => '2021-01-27 13:01:58'
            ],
            [
                'id' => '7',
                'name' => 'smtp_host',
                'value' => '',
                'created' => '2021-01-27 12:56:52',
                'modified' => '2021-01-27 13:01:58'
            ],
            [
                'id' => '8',
                'name' => 'smtp_user',
                'value' => '',
                'created' => '2021-01-27 12:56:52',
                'modified' => '2021-01-27 13:01:58'
            ],
            [
                'id' => '9',
                'name' => 'smtp_password',
                'value' => '',
                'created' => '2021-01-27 12:56:52',
                'modified' => '2021-01-27 13:01:58'
            ],
            [
                'id' => '10',
                'name' => 'smtp_port',
                'value' => '',
                'created' => '2021-01-27 12:56:52',
                'modified' => '2021-01-27 13:01:58'
            ],
            [
                'id' => '11',
                'name' => 'admin_list_num',
                'value' => '10',
                'created' => '2021-01-27 12:56:52',
                'modified' => '2021-01-27 13:01:58'
            ],
            [
                'id' => '12',
                'name' => 'google_analytics_id',
                'value' => '',
                'created' => '2021-01-27 12:56:52',
                'modified' => '2021-01-27 13:01:58'
            ],
            [
                'id' => '13',
                'name' => 'content_categories',
                'value' => 'YTowOnt9',
                'created' => '2021-01-27 12:56:52',
                'modified' => '2021-01-27 12:58:25'
            ],
            [
                'id' => '14',
                'name' => 'content_types',
                'value' => 'YTozOntzOjk6IuODluODreOCsCI7czo5OiLjg5bjg63jgrAiO3M6OToi44Oa44O844K4IjtzOjk6IuODmuODvOOCuCI7czo5OiLjg6Hjg7zjg6siO3M6OToi44Oh44O844OrIjt9',
                'created' => '2021-01-27 12:56:52',
                'modified' => '2021-01-27 12:58:25'
            ],
            [
                'id' => '15',
                'name' => 'category_permission',
                'value' => '',
                'created' => '2021-01-27 12:56:52',
                'modified' => '2021-01-27 12:58:25'
            ],
            [
                'id' => '16',
                'name' => 'admin_theme',
                'value' => '',
                'created' => '2021-01-27 12:56:52',
                'modified' => '2021-01-27 12:58:25'
            ],
            [
                'id' => '17',
                'name' => 'login_credit',
                'value' => '1',
                'created' => '2021-01-27 12:56:52',
                'modified' => '2021-01-27 13:01:58'
            ],
            [
                'id' => '18',
                'name' => 'first_access',
                'value' => '',
                'created' => '2021-01-27 12:56:52',
                'modified' => '2021-01-27 13:01:32'
            ],
            [
                'id' => '19',
                'name' => 'editor',
                'value' => 'BcCkeditor',
                'created' => '2021-01-27 12:56:52',
                'modified' => '2021-01-27 13:01:58'
            ],
            [
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
            ],
            [
                'id' => '21',
                'name' => 'editor_enter_br',
                'value' => '0',
                'created' => '2021-01-27 12:56:52',
                'modified' => '2021-01-27 13:01:58'
            ],
            [
                'id' => '22',
                'name' => 'admin_side_banner',
                'value' => '1',
                'created' => '2021-01-27 12:56:53',
                'modified' => '2021-01-27 13:01:58'
            ],
            [
                'id' => '23',
                'name' => 'smtp_tls',
                'value' => '0',
                'created' => '2021-01-27 12:56:53',
                'modified' => '2021-01-27 13:01:58'
            ],
            [
                'id' => '24',
                'name' => 'version',
                'value' => '3.0.6.1',
                'created' => '2021-01-27 12:58:10',
                'modified' => '2021-01-27 12:58:25'
            ],
            [
                'id' => '25',
                'name' => 'use_universal_analytics',
                'value' => '1',
                'created' => '2021-01-27 12:58:10',
                'modified' => '2021-01-27 12:58:25'
            ],
            [
                'id' => '26',
                'name' => 'google_maps_api_key',
                'value' => '',
                'created' => '2021-01-27 12:58:10',
                'modified' => '2021-01-27 12:58:25'
            ],
            [
                'id' => '27',
                'name' => 'main_site_display_name',
                'value' => 'パソコン',
                'created' => '2021-01-27 12:58:10',
                'modified' => '2021-01-27 12:58:25'
            ],
            [
                'id' => '28',
                'name' => 'use_site_device_setting',
                'value' => '1',
                'created' => '2021-01-27 12:58:10',
                'modified' => '2021-01-27 12:58:25'
            ],
            [
                'id' => '29',
                'name' => 'use_site_lang_setting',
                'value' => '0',
                'created' => '2021-01-27 12:58:10',
                'modified' => '2021-01-27 12:58:25'
            ],
            [
                'id' => '30',
                'name' => 'contents_sort_last_modified',
                'value' => '',
                'created' => '2021-01-27 12:58:10',
                'modified' => '2021-01-27 12:58:25'
            ],
            [
                'id' => '31',
                'name' => 'editor',
                'value' => 'BcCkeditor',
                'created' => '2021-01-27 12:58:10',
                'modified' => '2021-01-27 12:58:25'
            ],
        ];
        $table = $this->table('site_configs');
        $table->insert($data)->save();
    }

}
