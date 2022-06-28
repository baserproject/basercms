<?php
declare(strict_types=1);

namespace BaserCore\Test\Factory;

use CakephpFixtureFactories\Factory\BaseFactory as CakephpBaseFactory;
use Faker\Generator;

/**
 * PageFactory
 *
 * @method \BaserCore\Model\Entity\Page getEntity()
 * @method \BaserCore\Model\Entity\Page[] getEntities()
 * @method \BaserCore\Model\Entity\Page|\BaserCore\Model\Entity\Page[] persist()
 * @method static \BaserCore\Model\Entity\Page get(mixed $primaryKey, array $options = [])
 */
class PageFactory extends CakephpBaseFactory
{
    /**
     * Defines the Table Registry used to generate entities with
     *
     * @return string
     */
    protected function getRootTableRegistryName(): string
    {
        return 'BaserCore.Pages';
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
