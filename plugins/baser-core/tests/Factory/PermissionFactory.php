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

namespace BaserCore\Test\Factory;

use CakephpFixtureFactories\Factory\BaseFactory as CakephpBaseFactory;
use Faker\Generator;

/**
 * PermissionFactory
 */
class PermissionFactory extends CakephpBaseFactory
{
    /**
     * Defines the Table Registry used to generate entities with
     *
     * @return string
     */
    protected function getRootTableRegistryName(): string
    {
        return 'BaserCore.Permissions';
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
                'name' => $faker->text(),
                'no' => $faker->randomNumber(),
                'sort' => $faker->randomNumber(),
                'url' => $faker->text(),
                'auth' => true,
                'permission_group_id' => $faker->randomNumber()
            ];
        });
    }

    /**
     * ゲストに許可するURLを登録
     *
     * @param string $url
     * @return PermissionFactory
     */
    public function allowGuest(string $url)
    {
        return $this->setField('user_group_id', "0")
            ->setField('auth', true)
            ->setField('status', true)
            ->setField('url', $url);
    }

}
