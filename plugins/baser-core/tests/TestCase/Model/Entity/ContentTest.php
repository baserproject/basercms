<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.4.3
 * @license       https://basercms.net/license/index.html MIT License
 */

namespace BaserCore\Test\TestCase\Model\Entity;

use BaserCore\Model\Entity\Content;
use BaserCore\Test\Factory\ContentFactory;
use BaserCore\TestSuite\BcTestCase;

class ContentTest extends BcTestCase
{

    /**
     * test isAccessible
     * 新規作成時は ID・種類・紐付け先も代入できる
     */
    public function testIsAccessible_new()
    {
        $content = new Content();
        foreach (['id', 'plugin', 'type', 'entity_id', 'title'] as $field) {
            $this->assertTrue($content->isAccessible($field));
        }
    }

    /**
     * test isAccessible
     * 既存のコンテンツでは ID・種類・紐付け先を送信値で書き換えられない
     */
    public function testIsAccessible_existing()
    {
        ContentFactory::make(['id' => 100, 'plugin' => 'BaserCore', 'type' => 'Page', 'entity_id' => 1, 'title' => 'test'])->persist();
        $contents = $this->getTableLocator()->get('BaserCore.Contents');
        $content = $contents->get(100);
        foreach (['id', 'plugin', 'type', 'entity_id'] as $field) {
            $this->assertFalse($content->isAccessible($field));
        }
        $this->assertTrue($content->isAccessible('title'));

        $content = $contents->patchEntity($content, [
            'id' => 999,
            'plugin' => 'BcBlog',
            'type' => 'BlogContent',
            'entity_id' => 999,
            'title' => 'changed'
        ], ['validate' => false]);
        $this->assertEquals(100, $content->id);
        $this->assertEquals('BaserCore', $content->plugin);
        $this->assertEquals('Page', $content->type);
        $this->assertEquals(1, $content->entity_id);
        $this->assertEquals('changed', $content->title);
    }

}
