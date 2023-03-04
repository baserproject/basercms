<?php
declare(strict_types=1);

use BaserCore\Database\Migration\BcMigration;

class CreateCustomEntries1 extends BcMigration
{
    /**
     * Up Method.
     *
     * More information on this method is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-up-method
     * @return void
     */
    public function up()
    {
        $this->table('custom_entry_1_recruit')
            ->addColumn('custom_table_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('parent_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('name', 'text', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('title', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('level', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('lft', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('rght', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('creator_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('status', 'boolean', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('publish_begin', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('publish_end', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('published', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('created', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('modified', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('recruit_category', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('feature', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('main_visual', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('our_business', 'text', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('work_charm', 'text', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('occupation_and_infrastructure', 'text', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('company_introduction', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('recruitment_type', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('employment_status', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('job_description', 'text', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('required_skills', 'text', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('salary', 'text', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('working_hours', 'text', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('work_location', 'text', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('desired_figure', 'text', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('selection_process', 'text', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('employment_status_group', 'text', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('employment_status_note', 'text', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('working_hours_group', 'text', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('working_hours_note', 'text', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('salary_group', 'text', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('salary_type', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('salary_min', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('salary_max', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('salary_note', 'text', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('zip', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('pref', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('address_1', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('address_2', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('work_location_note', 'text', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('textarea_small', 'text', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('textarea_small_2', 'text', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('etc', 'text', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('textarea_small_3', 'text', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('welcoming_skills', 'text', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('occupation_charm', 'text', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->create();
    }

    /**
     * Down Method.
     *
     * More information on this method is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-down-method
     * @return void
     */
    public function down()
    {
        $this->table('custom_entries')->drop()->save();
    }
}
