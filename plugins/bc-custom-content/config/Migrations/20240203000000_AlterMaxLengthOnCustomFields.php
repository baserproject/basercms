<?php
declare(strict_types=1);

use BaserCore\Database\Migration\BcMigration;

class AlterMaxLengthOnCustomFields extends BcMigration
{
    /**
     * change
     * @return void
     */
    public function change()
    {
        $this->table('custom_fields')
            ->changeColumn('max_length', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->update();
    }

}
