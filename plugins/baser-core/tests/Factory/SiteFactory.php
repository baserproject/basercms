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
                'name' => $faker->text
            ];
        });
    }

    /**
     * メインサイトに設定する
     * @return SiteFactory
     */
    public function main(): SiteFactory
    {
        return $this->setField('id', 1)
            ->setField('status', true);
    }

    /**
     * スマホサイトに設定する
     * @return SiteFactory
     */
    public function smartphone($id = null, $mainSiteId = 1): SiteFactory
    {
        return $this->setField('id', $id ?: null)
            ->setField('main_site_id', $mainSiteId)
            ->setField('name', 'smartphone')
            ->setField('display_name', 'スマホサイト')
            ->setField('title', 'スマホサイト')
            ->setField('alias', 's')
            ->setField('use_subdomain', false)
            ->setField('relate_main_site', true)
            ->setField('device', 'smartphone')
            ->setField('same_main_url', false)
            ->setField('auto_redirect', true)
            ->setField('auto_link', true)
            ->setField('domain_type', null);
    }

    /**
     * 英語サイトに設定する
     * @return SiteFactory
     */
    public function english($id = null, $mainSiteId = 1): SiteFactory
    {
        return $this->setField('id', $id ?: null)
            ->setField('main_site_id', $mainSiteId)
            ->setField('name', 'english')
            ->setField('display_name', '英語サイト')
            ->setField('title', '英語サイト')
            ->setField('alias', 'en')
            ->setField('use_subdomain', false)
            ->setField('relate_main_site', false)
            ->setField('device', null)
            ->setField('same_main_url', false)
            ->setField('auto_redirect', true)
            ->setField('auto_link', false)
            ->setField('domain_type', null);
    }

    /**
     * 別ドメインのサイトに設定する
     * @return SiteFactory
     */
    public function anotherDomain($id = null, $mainSiteId = 1): SiteFactory
    {
        return $this->setField('id', $id ?: null)
            ->setField('main_site_id', $mainSiteId)
            ->setField('name', 'another')
            ->setField('display_name', '別ドメイン')
            ->setField('title', '別ドメイン')
            ->setField('alias', 'example.com')
            ->setField('relate_main_site', false)
            ->setField('device', null)
            ->setField('same_main_url', false)
            ->setField('auto_redirect', false)
            ->setField('auto_link', false)
            ->setField('use_subdomain', true)
            ->setField('domain_type', 2);
    }

    /**
     * サブドメインのサイトに設定する
     * @return SiteFactory
     */
    public function subDomain($id = null, $mainSiteId = 1): SiteFactory
    {
        return $this->setField('id', $id ?: null)
            ->setField('main_site_id', $mainSiteId)
            ->setField('name', 'another')
            ->setField('display_name', 'サブドメイン')
            ->setField('title', 'サブドメイン')
            ->setField('alias', 'sub')
            ->setField('relate_main_site', false)
            ->setField('device', null)
            ->setField('same_main_url', false)
            ->setField('auto_redirect', false)
            ->setField('auto_link', false)
            ->setField('use_subdomain', true)
            ->setField('domain_type', 1);
    }

}
