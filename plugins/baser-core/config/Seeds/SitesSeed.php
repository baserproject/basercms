<?php
declare(strict_types=1);

use BaserCore\Database\Migration\BcSeed;

/**
 * Sites seed.
 */
class SitesSeed extends BcSeed
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
                'main_site_id' => NULL,
                'name' => '',
                'display_name' => 'メインサイト',
                'title' => 'メインサイト',
                'alias' => '',
                'theme' => 'BcFront',
                'status' => '1',
                'keyword' => '',
                'description' => '',
                'use_subdomain' => '0',
                'relate_main_site' => '0',
                'device' => NULL,
                'lang' => NULL,
                'same_main_url' => '0',
                'auto_redirect' => '0',
                'auto_link' => '0',
                'domain_type' => '0',
                'created' => NULL,
                'modified' => NULL,
            ],
        ];

        $table = $this->table('sites');
        $table->insert($data)->save();
    }
}
