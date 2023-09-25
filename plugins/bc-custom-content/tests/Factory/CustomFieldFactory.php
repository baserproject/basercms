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

use CakephpFixtureFactories\Factory\BaseFactory as CakephpBaseFactory;
use Faker\Generator;

/**
 * CustomFieldFactory
 */
class CustomFieldFactory extends CakephpBaseFactory
{

    /**
     * Defines the Table Registry used to generate entities with
     *
     * @return string
     */
    protected function getRootTableRegistryName(): string
    {
        return 'BcCustomContent.CustomFields';
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
                'title' => 'test',
                'status' => 1,
                'default_value' => '',
                'validate' => '',
                'regex' => '',
                'regex_error_message' => '',
                'counter' => 0,
                'auto_convert' => '',
                'placeholder' => '',
                'size' => NULL,
                'max_length' => NULL,
                'source' => '',
                'created' => '2023-01-30 06:22:47',
                'modified' => '2023-02-20 11:18:32',
                'line' => NULL
            ];
        });
    }

}
