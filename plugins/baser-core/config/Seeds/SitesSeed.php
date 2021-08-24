<?php
declare(strict_types=1);

use Migrations\AbstractSeed;

/**
 * Sites seed.
 */
class SitesSeed extends AbstractSeed
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
                'main_site_id' => NULL,
                'name' => '',
                'display_name' => 'メインサイト',
                'title' => 'メインサイト',
                'alias' => '',
                'theme' => 'BcSample',
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
                'created' => '2020-12-14 14:48:33',
                'modified' => '2020-12-14 14:48:33',
            ],
        ];

        $table = $this->table('sites');
        $table->insert($data)->save();
    }
}
