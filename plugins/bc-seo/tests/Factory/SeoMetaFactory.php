<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.2.0
 * @license       https://basercms.net/license/index.html MIT License
 */

namespace BcSeo\Test\Factory;

use CakephpFixtureFactories\Factory\BaseFactory as CakephpBaseFactory;
use Faker\Generator;

/**
 * SeoMetaFactory
 */
class SeoMetaFactory extends CakephpBaseFactory
{
    /**
     * getRootTableRegistryName
     */
    protected function getRootTableRegistryName(): string
    {
        return 'BcSeo.SeoMetas';
    }

    /**
     * setDefaultTemplate
     */
    protected function setDefaultTemplate(): void
    {
        $this->setDefaultData(function (Generator $faker) {
            return [
                'table_id' => 0,
                'description' => $faker->text(),
            ];
        });
    }
}
