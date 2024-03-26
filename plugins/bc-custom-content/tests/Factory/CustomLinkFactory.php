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
 * CustomTableFactory
 */
class CustomLinkFactory extends CakephpBaseFactory
{

    /**
     * Defines the Table Registry used to generate entities with
     *
     * @return string
     */
    protected function getRootTableRegistryName(): string
    {
        return 'BcCustomContent.CustomLinks';
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
                'no' => NULL,
                'custom_table_id' => 1,
                'custom_field_id' => 1,
                'parent_id' => NULL,
                'lft' => 1,
                'rght' => 2,
                'level' => 0,
                'name' => $faker->text(5),
                'title' => $faker->text(5),
                'group_valid' => 0,
                'created' => FrozenTime::now(),
                'modified' => FrozenTime::now(),
                'use_loop' => 0,
                'display_admin_list' => 1,
                'use_api' => 1,
                'search_target_front' => 1,
                'before_linefeed' => 0,
                'after_linefeed' => 0,
                'display_front' => 1,
                'search_target_admin' => 1,
                'description' => NULL,
                'attention' => NULL,
                'before_head' => NULL,
                'after_head' => NULL,
                'options' => NULL,
                'class' => NULL,
                'status' => 1,
                'required' => NULL,
            ];
        });
    }

}
