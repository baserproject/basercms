<?php
declare(strict_types=1);

use BaserCore\Database\Migration\BcSeed;

/**
 * ThemeConfigs seed.
 */
class ThemeConfigsSeed extends BcSeed
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
                'id' => 1,
                'name' => 'logo',
                'value' => '',
                'created' => NULL,
                'modified' => NULL,
            ],
            [
                'id' => 2,
                'name' => 'logo_alt',
                'value' => 'baserCMS',
                'created' => NULL,
                'modified' => NULL,
            ],
            [
                'id' => 3,
                'name' => 'logo_link',
                'value' => '/',
                'created' => NULL,
                'modified' => NULL,
            ],
            [
                'id' => 4,
                'name' => 'main_image_1',
                'value' => '',
                'created' => NULL,
                'modified' => NULL,
            ],
            [
                'id' => 5,
                'name' => 'main_image_alt_1',
                'value' => 'コーポレートサイトにちょうどいい国産CMS',
                'created' => NULL,
                'modified' => NULL,
            ],
            [
                'id' => 6,
                'name' => 'main_image_link_1',
                'value' => '/',
                'created' => NULL,
                'modified' => NULL,
            ],
            [
                'id' => 7,
                'name' => 'main_image_2',
                'value' => '',
                'created' => NULL,
                'modified' => NULL,
            ],
            [
                'id' => 8,
                'name' => 'main_image_alt_2',
                'value' => '全て日本語の国産CMSだから、設置も更新も簡単、わかりやすい。',
                'created' => NULL,
                'modified' => NULL,
            ],
            [
                'id' => 9,
                'name' => 'main_image_link_2',
                'value' => '/',
                'created' => NULL,
                'modified' => NULL,
            ],
            [
                'id' => 10,
                'name' => 'main_image_3',
                'value' => '',
                'created' => NULL,
                'modified' => NULL,
            ],
            [
                'id' => 11,
                'name' => 'main_image_alt_3',
                'value' => '標準的なWebサイトに必要な基本機能を全て装備',
                'created' => NULL,
                'modified' => NULL,
            ],
            [
                'id' => 12,
                'name' => 'main_image_link_3',
                'value' => '/',
                'created' => NULL,
                'modified' => NULL,
            ],
            [
                'id' => 13,
                'name' => 'main_image_4',
                'value' => '',
                'created' => NULL,
                'modified' => NULL,
            ],
            [
                'id' => 14,
                'name' => 'main_image_alt_4',
                'value' => 'デザインも自由自在にカスタマイズ可能！',
                'created' => NULL,
                'modified' => NULL,
            ],
            [
                'id' => 15,
                'name' => 'main_image_link_4',
                'value' => '/',
                'created' => NULL,
                'modified' => NULL,
            ],
            [
                'id' => 16,
                'name' => 'main_image_5',
                'value' => '',
                'created' => NULL,
                'modified' => NULL,
            ],
            [
                'id' => 17,
                'name' => 'main_image_alt_5',
                'value' => '質問・ご相談はユーザーズフォーラムへ',
                'created' => NULL,
                'modified' => NULL,
            ],
            [
                'id' => 18,
                'name' => 'main_image_link_5',
                'value' => '/',
                'created' => NULL,
                'modified' => NULL,
            ],
            [
                'id' => 19,
                'name' => 'color_main',
                'value' => '2c3adb',
                'created' => NULL,
                'modified' => NULL,
            ],
            [
                'id' => 20,
                'name' => 'color_sub',
                'value' => '001800',
                'created' => NULL,
                'modified' => NULL,
            ],
            [
                'id' => 21,
                'name' => 'color_link',
                'value' => '2B7BB9',
                'created' => NULL,
                'modified' => NULL,
            ],
            [
                'id' => 22,
                'name' => 'color_hover',
                'value' => '2B7BB9',
                'created' => NULL,
                'modified' => NULL,
            ],
        ];

        $table = $this->table('theme_configs');
        $table->insert($data)->save();
    }
}
