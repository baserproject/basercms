<?php
declare(strict_types=1);

namespace BaserCore\Test\Factory;

use Cake\I18n\FrozenTime;
use CakephpFixtureFactories\Factory\BaseFactory as CakephpBaseFactory;
use Faker\Generator;

/**
 * ContentFactory
 *
 * @method \BaserCore\Model\Entity\Content getEntity()
 * @method \BaserCore\Model\Entity\Content[] getEntities()
 * @method \BaserCore\Model\Entity\Content|\BaserCore\Model\Entity\Content[] persist()
 * @method static \BaserCore\Model\Entity\Content get(mixed $primaryKey, array $options = [])
 */
class ContentFactory extends CakephpBaseFactory
{
    /**
     * Defines the Table Registry used to generate entities with
     *
     * @return string
     */
    protected function getRootTableRegistryName(): string
    {
        return 'BaserCore.Contents';
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
                'name' => $faker->text(50),
                'plugin' => 'BaserCore',
                'type' => 'Page',
                'entity_id' => $faker->randomNumber(1, 100),
                'url' => '',
                'site_id' => 1,
                'alias_id' => null,
                'main_site_content_id' => null,
                'parent_id' => null,
                'lft' => null,
                'rght' => null,
                'level' => 1,
                'title' => $faker->title(50),
                'eyecatch' => null,
                'author_id' => 1,
                'layout_template' => 'default',
                'status' => true,
                'publish_begin' => null,
                'publish_end' => null,
                'self_status' => true,
                'self_publish_begin' => null,
                'self_publish_end' => null,
                'exclude_search' => false,
                'created_date' => FrozenTime::now(),
                'modified_date' => FrozenTime::now(),
                'site_root' => false,
                'deleted_date' => null,
                'exclude_menu' => false,
                'blank_link' => false,
            ];
        });
    }

    /**
     * トップページとして設定する
     * トップだけでよいのでルーティングが必要とするテスト用
     * @return ContentFactory
     */
    public function top()
    {
        return $this->setField('url', '/')
            ->setField('site_id', 1)
            ->setField('status', true);
    }

    /**
     * ツリーのノードとしてのデータを生成する
     *
     * lft / rght はセットしない
     * TreeBehavior::recover() でセットすること
     * @return ContentFactory
     */
    public function treeNode($id, $siteId, $parentId, $name, $url, $entityId, $siteRoot = false, $title='title')
    {
        return $this->setField('id', $id)
            ->setField('site_id', $siteId)
            ->setField('parent_id', $parentId)
            ->setField('name', $name)
            ->setField('url', $url)
            ->setField('entity_id', $entityId)
            ->setField('status', true)
            ->setField('title', $title)
            ->setField('site_root', $siteRoot);
    }

}
