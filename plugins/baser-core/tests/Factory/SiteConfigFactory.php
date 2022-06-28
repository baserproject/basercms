<?php
declare(strict_types=1);

namespace BaserCore\Test\Factory;

use CakephpFixtureFactories\Factory\BaseFactory as CakephpBaseFactory;
use Faker\Generator;

/**
 * SiteConfigFactory
 *
 * @method \BaserCore\Model\Entity\SiteConfig getEntity()
 * @method \BaserCore\Model\Entity\SiteConfig[] getEntities()
 * @method \BaserCore\Model\Entity\SiteConfig|\BaserCore\Model\Entity\SiteConfig[] persist()
 * @method static \BaserCore\Model\Entity\SiteConfig get(mixed $primaryKey, array $options = [])
 */
class SiteConfigFactory extends CakephpBaseFactory
{
    /**
     * Defines the Table Registry used to generate entities with
     *
     * @return string
     */
    protected function getRootTableRegistryName(): string
    {
        return 'BaserCore.SiteConfigs';
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
