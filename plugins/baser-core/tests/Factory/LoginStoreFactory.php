<?php
declare(strict_types=1);

namespace BaserCore\Test\Factory;

use Cake\I18n\FrozenTime;
use CakephpFixtureFactories\Factory\BaseFactory as CakephpBaseFactory;
use Faker\Generator;

/**
 * LoginStoreFactory
 *
 */
class LoginStoreFactory extends CakephpBaseFactory
{
    /**
     * Defines the Table Registry used to generate entities with
     *
     * @return string
     */
    protected function getRootTableRegistryName(): string
    {
        return 'BaserCore.LoginStores';
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
                'user_id' => $faker->randomNumber(1, 100),
                'store_key' => $faker->text(255),
                'prefix' => $faker->text(5),
                'created' => FrozenTime::now(),
                'modified' => FrozenTime::now(),
            ];
        });
    }
}
