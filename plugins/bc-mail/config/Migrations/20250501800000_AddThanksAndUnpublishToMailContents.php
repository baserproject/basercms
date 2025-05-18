<?php
declare(strict_types=1);

use BaserCore\Database\Migration\BcMigration;

class AddThanksAndUnpublishToMailContents extends BcMigration
{
    /**
     * Up Method.
     * @return void
     */
    public function up()
    {
        $this->table('mail_contents')
            ->addColumn('thanks', 'text', [
                'after' => 'description',
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('unpublish', 'text', [
                'after' => 'thanks',
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->update();
    }

    /**
     * Down Method.
     * @return void
     */
    public function down()
    {
        $this->table('mail_contents')
            ->removeColumn('thanks')
            ->removeColumn('unpublish')
            ->update();
    }
}
