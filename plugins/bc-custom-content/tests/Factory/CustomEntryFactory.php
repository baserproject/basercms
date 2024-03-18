<?php
declare(strict_types=1);

/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */

namespace BcCustomContent\Test\Factory;

use Cake\I18n\FrozenTime;
use CakephpFixtureFactories\Factory\BaseFactory as CakephpBaseFactory;
use Faker\Generator;

/**
 * CustomEntryFactory
 */
class CustomEntryFactory extends CakephpBaseFactory
{

    /**
     * Defines the Table Registry used to generate entities with
     *
     * @return string
     */
    protected function getRootTableRegistryName(): string
    {
        return 'custom_entry_1_recruit_categories';
    }

    /**
     * Defines the factory's default values. This is useful for
     * not nullable fields. You may use methods of the present factory here too.
     *
     * @return void
     */
    protected function setDefaultTemplate(): void
    {
        $this->setDefaultData(function (Generator $faker) {
            return [
                'has_child' => 1,
                'custom_table_id' => $faker->randomNumber(1, 100),
                'published' => FrozenTime::now(),
                'modified' => FrozenTime::now(),
                'created' => FrozenTime::now(),
                'name' => $faker->text(5),
                'recruit_category' => $faker->randomNumber(1, 100),
                'feature' => $faker->text(5),
                'main_visual' => $faker->text(5),
                'our_business' => $faker->text(5),
                'occupation_and_infrastructure' => $faker->text(5),
                'company_introduction' => $faker->text(5),
                'recruitment_type' => $faker->text(5),
                'employment_status' => $faker->text(5),
                'job_description' => $faker->text(5),
                'title' => $faker->text(5),
                'status' => 1,
                'publish_begin' => NULL,
                'publish_end' => NULL,
                'employment_status_group' => NULL,
                'employment_status_note' => $faker->text(5),
                'working_hours_group' => NULL,
                'working_hours_note' => $faker->text(5),
                'salary_group' => NULL,
                'salary_type' => $faker->text(5),
                'salary_min' => $faker->randomNumber(1, 100),
                'salary_max' => $faker->randomNumber(1, 100),
                'salary_note' => $faker->text(5),
                'creator_id' => $faker->randomNumber(1, 100),
                'textarea_small' => $faker->text(5),
                'textarea_small_2' => $faker->text(5),
                'etc' => $faker->text(5),
                'textarea_small_3' => $faker->text(5),
                'welcoming_skills' => $faker->text(5),
                'occupation_charm' => $faker->text(5),
            ];
        });
    }

}
