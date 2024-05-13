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
        // 2024/02/27 ryuring
        // PostgreSQLのカラム変更ができないため、20230117094760_CreateCustomFields.php にて直接変更し
        // こちらでは何もしないように調整
        // phinxでの管理に影響する可能性があるため削除しないおく

//        $this->table('custom_fields')
//            ->changeColumn('max_length', 'integer', [
//                'default' => null,
//                'limit' => null,
//                'null' => true,
//            ])
//            ->update();
    }

}
