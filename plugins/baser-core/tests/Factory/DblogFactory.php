<?php
declare(strict_types=1);

namespace BaserCore\Test\Factory;

use Cake\I18n\FrozenTime;
use CakephpFixtureFactories\Factory\BaseFactory as CakephpBaseFactory;
use Faker\Generator;

/**
 * DblogFactory
 */
class DblogFactory extends CakephpBaseFactory
{

    /**
     * Defines the Table Registry used to generate entities with
     *
     * @return string
     */
    protected function getRootTableRegistryName(): string
    {
        return 'BaserCore.Dblogs';
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
                'message' => $faker->text(50),
                'user_id' => $faker->randomNumber(1, 100),
                'created' => FrozenTime::now(),
                'modified' => FrozenTime::now()
            ];
        });
    }

}
