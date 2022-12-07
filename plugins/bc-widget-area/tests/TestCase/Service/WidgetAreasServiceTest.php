<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */

namespace BcWidgetArea\Test\TestCase\Service;

use BaserCore\TestSuite\BcTestCase;

/**
 * WidgetAreasServiceTest
 */
class WidgetAreasServiceTest extends BcTestCase
{

    /**
     * set up
     */
    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * tear down
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * コントロールソース取得
     *
     * @param string $field
     */
    public function testGetControlSource()
    {
        $this->markTestIncomplete('このテストはまだ確認できていません。WidgetAreasTableより移行済');
        $result = $this->WidgetArea->getControlSource('id');
        $this->assertEquals([1 => 'ウィジェットエリア', 2 => 'ブログサイドバー'], $result, 'コントロールソースを取得できません');
    }

}
