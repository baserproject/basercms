<?php
declare(strict_types=1);

namespace BaserCore\Test\Factory;

use CakephpFixtureFactories\Factory\BaseFactory as CakephpBaseFactory;
use Faker\Generator;

/**
 * PluginFactory
 *
 * @method \BaserCore\Model\Entity\Plugin getEntity()
 * @method \BaserCore\Model\Entity\Plugin[] getEntities()
 * @method \BaserCore\Model\Entity\Plugin|\BaserCore\Model\Entity\Plugin[] persist()
 * @method static \BaserCore\Model\Entity\Plugin get(mixed $primaryKey, array $options = [])
 */
class PluginFactory extends CakephpBaseFactory
{
    /**
     * Defines the Table Registry used to generate entities with
     *
     * @return string
     */
    protected function getRootTableRegistryName(): string
    {
        return 'BaserCore.Plugins';
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
                'name' => $faker->word(),
                'title' => $faker->word(),
                'version' => '1.0.0',
                'status' => true,
                'db_init' => true,
                'priority' => $faker->unique()->randomNumber(),
            ];
        });
    }
}
