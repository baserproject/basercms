<?php
declare(strict_types=1);

use BaserCore\Database\Migration\BcSeed;

/**
 * SiteConfigs seed.
 */
class SiteConfigsSeed extends BcSeed
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
    public function run(): void
    {
        $data = [
            [
                'id' => '1',
                'name' => 'address',
                'value' => '福岡県',
                'created' => '',
                'modified' => ''
            ],
            [
                'id' => '2',
                'name' => 'email',
                'value' => 'basertest@example.com',
                'created' => '',
                'modified' => ''
            ],
            [
                'id' => '3',
                'name' => 'widget_area',
                'value' => '1',
                'created' => '',
                'modified' => ''
            ],
            [
                'id' => '4',
                'name' => 'maintenance',
                'value' => '0',
                'created' => '',
                'modified' => ''
            ],
            [
                'id' => '6',
                'name' => 'smtp_host',
                'value' => '',
                'created' => '',
                'modified' => ''
            ],
            [
                'id' => '7',
                'name' => 'smtp_user',
                'value' => '',
                'created' => '',
                'modified' => ''
            ],
            [
                'id' => '8',
                'name' => 'smtp_password',
                'value' => '',
                'created' => '',
                'modified' => ''
            ],
            [
                'id' => '9',
                'name' => 'smtp_port',
                'value' => '',
                'created' => '',
                'modified' => ''
            ],
            [
                'id' => '10',
                'name' => 'admin_list_num',
                'value' => '10',
                'created' => '',
                'modified' => ''
            ],
            [
                'id' => '11',
                'name' => 'google_analytics_id',
                'value' => '',
                'created' => '',
                'modified' => ''
            ],
            [
                'id' => '12',
                'name' => 'content_types',
                'value' => 'YTozOntzOjk6IuODluODreOCsCI7czo5OiLjg5bjg63jgrAiO3M6OToi44Oa44O844K4IjtzOjk6IuODmuODvOOCuCI7czo5OiLjg6Hjg7zjg6siO3M6OToi44Oh44O844OrIjt9',
                'created' => '',
                'modified' => ''
            ],
            [
                'id' => '13',
                'name' => 'admin_theme',
                'value' => '',
                'created' => '',
                'modified' => ''
            ],
            [
                'id' => '14',
                'name' => 'login_credit',
                'value' => '1',
                'created' => '',
                'modified' => ''
            ],
            [
                'id' => '15',
                'name' => 'first_access',
                'value' => '',
                'created' => '',
                'modified' => ''
            ],
            [
                'id' => '16',
                'name' => 'editor',
                'value' => 'BaserCore.BcCkeditor',
                'created' => '',
                'modified' => ''
            ],
            [
                'id' => '17',
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
                'created' => '',
                'modified' => ''
            ],
            [
                'id' => '18',
                'name' => 'editor_enter_br',
                'value' => '0',
                'created' => '',
                'modified' => ''
            ],
            [
                'id' => '19',
                'name' => 'admin_side_banner',
                'value' => '1',
                'created' => '',
                'modified' => ''
            ],
            [
                'id' => '20',
                'name' => 'smtp_tls',
                'value' => '0',
                'created' => '',
                'modified' => ''
            ],
            [
                'id' => '21',
                'name' => 'version',
                'value' => '3.0.6.1',
                'created' => '',
                'modified' => ''
            ],
            [
                'id' => '22',
                'name' => 'google_maps_api_key',
                'value' => '',
                'created' => '',
                'modified' => ''
            ],
            [
                'id' => '23',
                'name' => 'use_site_device_setting',
                'value' => '1',
                'created' => '',
                'modified' => ''
            ],
            [
                'id' => '24',
                'name' => 'use_site_lang_setting',
                'value' => '0',
                'created' => '',
                'modified' => ''
            ],
            [
                'id' => '25',
                'name' => 'contents_sort_last_modified',
                'value' => '',
                'created' => '',
                'modified' => ''
            ],
            [
                'id' => '26',
                'name' => 'mail_additional_parameters',
                'value' => '1',
                'created' => '',
                'modified' => ''
            ],
            [
                'id' => '27',
                'name' => 'use_update_notice',
                'value' => '1',
                'created' => '',
                'modified' => ''
            ],
            [
                'id' => '28',
                'name' => 'outer_service_output_header',
                'value' => '',
                'created' => '',
                'modified' => ''
            ],
            [
                'id' => '29',
                'name' => 'outer_service_output_footer',
                'value' => '',
                'created' => '',
                'modified' => ''
            ],
        ];
        $table = $this->table('site_configs');
        $table->insert($data)->save();
    }

}
