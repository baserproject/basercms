<?php
declare(strict_types=1);

namespace BaserCore\Test\Factory;

use CakephpFixtureFactories\Factory\BaseFactory as CakephpBaseFactory;
use Faker\Generator;

/**
 * SiteFactory
 *
 * @method \BaserCore\Model\Entity\Site getEntity()
 * @method \BaserCore\Model\Entity\Site[] getEntities()
 * @method \BaserCore\Model\Entity\Site|\BaserCore\Model\Entity\Site[] persist()
 * @method static \BaserCore\Model\Entity\Site get(mixed $primaryKey, array $options = [])
 */
class SiteFactory extends CakephpBaseFactory
{
    /**
     * Defines the Table Registry used to generate entities with
     *
     * @return string
     */
    protected function getRootTableRegistryName(): string
    {
        return 'BaserCore.Sites';
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
                // set the model's default values
                // For example:
                // 'name' => $faker->lastName
            ];
        });
    }
}
