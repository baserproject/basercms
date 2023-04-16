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
 * UserFactory
 *
 * @method \BaserCore\Model\Entity\User getEntity()
 * @method \BaserCore\Model\Entity\User[] getEntities()
 * @method \BaserCore\Model\Entity\User|\BaserCore\Model\Entity\User[] persist()
 * @method static \BaserCore\Model\Entity\User get(mixed $primaryKey, array $options = [])
 */
class UserFactory extends CakephpBaseFactory
{
    /**
     * Defines the Table Registry used to generate entities with
     *
     * @return string
     */
    protected function getRootTableRegistryName(): string
    {
        return 'BaserCore.Users';
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
                'name' => $faker->text()
            ];
        });
    }

    /**
     * 無効ユーザーに設定する
     * @return UserFactory
     */
    public function suspended()
    {
        return $this->setField('status', false);
    }

    /**
     * 管理ユーザーに設定する
     * @return UserFactory
     */
    public function admin()
    {
        UserGroupFactory::make()->admins()->persist();
        UserGroupFactory::make(['name' => 'test group1', 'title' => 'test title1'])->persist();
        UserGroupFactory::make(['name' => 'test group2', 'title' => 'test title2'])->persist();
        UserGroupFactory::make(['name' => 'test group3', 'title' => 'test title3'])->persist();
        UsersUserGroupFactory::make()->admin()->persist();

        return $this->setField('id', 1)
            ->setField('email', 'admin@example.com')
            ->setField('name', 'name')
            ->setField('status', 1)
            ->setField('method', 'ALL');
    }
}
