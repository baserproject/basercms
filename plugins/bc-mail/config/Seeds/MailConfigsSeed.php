<?php
declare(strict_types=1);

use Migrations\AbstractSeed;

/**
 * MailConfigs seed.
 */
class MailConfigsSeed extends AbstractSeed
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
                'id' => 1,
                'site_name' => 'baserCMS - Based Website Development Project -',
                'site_url' => 'http://basercms.net/',
                'site_email' => 'info@basercms.net',
                'site_tel' => '',
                'site_fax' => '',
                'created' => '2016-08-12 00:48:33',
                'modified' => NULL,
            ],
        ];

        $table = $this->table('mail_configs');
        $table->insert($data)->save();
    }
}
