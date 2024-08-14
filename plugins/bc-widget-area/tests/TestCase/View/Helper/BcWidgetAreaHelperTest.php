<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @since           baserCMS v 3.0.0
 * @license         https://basercms.net/license/index.html
 */

namespace BcWidgetArea\Test\TestCase\View\Helper;

use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\View\BcFrontAppView;
use BaserCore\View\Helper\BcTextHelper;
use BcWidgetArea\Test\Factory\WidgetAreaFactory;
use BcWidgetArea\View\Helper\BcWidgetAreaHelper;
use Cake\View\View;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * text helper library.
 *
 * @property BcTextHelper $Helper
 * @property BcWidgetAreaHelper $BcWidgetArea
 */
class BcWidgetAreaHelperTest extends BcTestCase
{
    /**
     * ScenarioAwareTrait
     */
    use ScenarioAwareTrait;

    public function setUp(): void
    {
        parent::setUp();
        $View = new View();
        $this->BcWidgetArea = new BcWidgetAreaHelper($View);
    }

    public function tearDown(): void
    {
        unset($this->BcWidgetArea);
        parent::tearDown();
    }

    /**
     * ウィジェットエリアを表示する
     *
     * @dataProvider showDataProvider
     *
     * @param $no ウィジェットエリアNO
     * @param array $options オプション
     * @param string $expected 期待値
     */
    public function testShow($fileName, $no, $expected)
    {
        $this->loadFixtureScenario(InitAppScenario::class);
        $this->BcWidgetArea = new BcWidgetAreaHelper(new BcFrontAppView($this->loginAdmin($this->getRequest())));
        WidgetAreaFactory::make([
            'id' => 1,
            'name' => '標準サイドバー',
            'widgets' => 'YTozOntpOjA7YToxOntzOjc6IldpZGdldDIiO2E6OTp7czoyOiJpZCI7czoxOiIyIjtzOjQ6InR5cGUiO3M6MzM6IuODreODvOOCq+ODq+ODiuODk+OCsuODvOOCt+ODp+ODsyI7czo3OiJlbGVtZW50IjtzOjEwOiJsb2NhbF9uYXZpIjtzOjY6InBsdWdpbiI7czo5OiJCYXNlckNvcmUiO3M6NDoic29ydCI7aToxO3M6NDoibmFtZSI7czozMzoi44Ot44O844Kr44Or44OK44OT44Ky44O844K344On44OzIjtzOjU6ImNhY2hlIjtzOjE6IjEiO3M6OToidXNlX3RpdGxlIjtzOjE6IjEiO3M6Njoic3RhdHVzIjtzOjE6IjEiO319aToxO2E6MTp7czo3OiJXaWRnZXQzIjthOjg6e3M6MjoiaWQiO3M6MToiMyI7czo0OiJ0eXBlIjtzOjE4OiLjgrXjgqTjg4jlhoXmpJzntKIiO3M6NzoiZWxlbWVudCI7czo2OiJzZWFyY2giO3M6NjoicGx1Z2luIjtzOjk6IkJhc2VyQ29yZSI7czo0OiJzb3J0IjtpOjI7czo0OiJuYW1lIjtzOjE4OiLjgrXjgqTjg4jlhoXmpJzntKIiO3M6OToidXNlX3RpdGxlIjtzOjE6IjEiO3M6Njoic3RhdHVzIjtzOjE6IjEiO319aToyO2E6MTp7czo3OiJXaWRnZXQ0IjthOjk6e3M6MjoiaWQiO3M6MToiNCI7czo0OiJ0eXBlIjtzOjEyOiLjg4bjgq3jgrnjg4giO3M6NzoiZWxlbWVudCI7czo0OiJ0ZXh0IjtzOjY6InBsdWdpbiI7czo5OiJCYXNlckNvcmUiO3M6NDoic29ydCI7aTozO3M6NDoibmFtZSI7czo5OiLjg6rjg7Pjgq8iO3M6NDoidGV4dCI7czoyNzc6Ijx1bD48bGk+PGEgaHJlZj0iaHR0cHM6Ly9iYXNlcmNtcy5uZXQiIHRhcmdldD0iX2JsYW5rIj5iYXNlckNNU+OCquODleOCo+OCt+ODo+ODqzwvYT48L2xpPjwvdWw+PHA+PHNtYWxsPuOBk+OBrumDqOWIhuOBr+OAgeeuoeeQhueUu+mdouOBriBb6Kit5a6aXSDihpIgW+ODpuODvOODhuOCo+ODquODhuOCo10g4oaSIFvjgqbjgqPjgrjjgqfjg4Pjg4jjgqjjg6rjgqJdIOKGkiBb5qiZ5rqW44K144Kk44OJ44OQ44O8XSDjgojjgornt6jpm4bjgafjgY3jgb7jgZnjgII8L3NtYWxsPjwvcD4iO3M6OToidXNlX3RpdGxlIjtzOjE6IjEiO3M6Njoic3RhdHVzIjtzOjE6IjEiO319fQ=='
        ])->persist();

        ob_start();
        $this->BcWidgetArea->show($no);
        $result = ob_get_clean();

        $this->assertStringContainsString($expected, $result);
    }

    public static function showDataProvider()
    {
        return [
            ['test', 1, '<h2 class="bs-widget-head">サイト内検索</h2>'],
            ['test', 2, '']
        ];
    }

    /**
     * ウィジェットエリアを出力する
     *
     * $noを指定していない場合、ウィジェットが出力されません。
     *
     * @param string $url 現在のURL
     * @param int $no
     * @param string $expected 期待値
     * @dataProvider getWidgetAreaDataProvider
     * @TODO: $noが指定されてない(null)場合のテストを記述する
     */
    public function testGetWidgetArea($url, $no, $expected)
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
        App::uses('BlogHelper', 'BcBlog.View/Helper');
        $this->BcBaser->request = $this->_getRequest($url);
        $this->assertMatchesRegularExpression('/' . $expected . '/', $this->BcBaser->getWidgetArea($no));
    }

    public static function getWidgetAreaDataProvider()
    {
        return [
            ['/company', 1, '<div class="widget-area widget-area-1">'],
            ['/company', 2, '<div class="widget-area widget-area-2">'],
            ['/company', null, '<div class="widget-area widget-area-1">'],
        ];
    }

}
