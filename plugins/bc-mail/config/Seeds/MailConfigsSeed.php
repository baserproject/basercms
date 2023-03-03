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
    public function run(): void
    {
        $data = [
            [
                'id' => 1,
                'name' => 'site_name',
                'value' => 'My Site',
                'modified' => NULL,
                'created' => NULL,
            ],
            [
                'id' => 2,
                'name' => 'site_url',
                'value' => 'https://example.com/',
                'modified' => NULL,
                'created' => NULL,
            ],
            [
                'id' => 3,
                'name' => 'site_email',
                'value' => 'foo@example.com',
                'modified' => NULL,
                'created' => NULL,
            ],
            [
                'id' => 4,
                'name' => 'site_tel',
                'value' => NULL,
                'modified' => NULL,
                'created' => NULL,
            ],
            [
                'id' => 5,
                'name' => 'site_fax',
                'value' => NULL,
                'modified' => NULL,
                'created' => NULL,
            ],
        ];

        $table = $this->table('mail_configs');
        $table->insert($data)->save();
    }
}
