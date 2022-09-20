<?php
// TODO : コード確認要
return;
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @since           baserCMS v 3.0.0-beta
 * @license         https://basercms.net/license/index.html
 */
App::uses('Plugin', 'Model');

/**
 * Class PluginTest
 *
 * class NonAssosiationPlugin extends Plugin {
 *  public $name = 'Plugin';
 *  public $belongsTo = array();
 *  public $hasMany = array();
 * }
 *
 */
class PluginTest extends BaserTestCase
{

    public $fixtures = [
        'baser.Default.Favorite',
        'baser.Default.Page',
        'baser.Default.Plugin',
        'baser.Default.User',
        'baser.Default.Site',
        'baser.Default.SiteConfig',
    ];

    public function setUp()
    {
        parent::setUp();
        $this->Plugin = ClassRegistry::init('Plugin');
    }

    public function tearDown()
    {
        unset($this->Plugin);
        parent::tearDown();
    }

    /**
     * validate
     */
    public function test必須チェック()
    {
        $this->Plugin->create([
            'Plugin' => [
                'title' => 'baser',
            ]
        ]);
        $this->assertFalse($this->Plugin->validates());
        $this->assertArrayHasKey('name', $this->Plugin->validationErrors);
        $this->assertEquals('プラグイン名は半角英数字、ハイフン、アンダースコアのみが利用可能です。', current($this->Plugin->validationErrors['name']));
    }

    public function test桁数チェック正常系()
    {
        $this->Plugin->create([
            'Plugin' => [
                'name' => '12345678901234567890123456789012345678901234567890',
                'title' => '12345678901234567890123456789012345678901234567890',
            ]
        ]);
        $this->assertTrue($this->Plugin->validates());
    }

    public function test桁数チェック異常系()
    {
        $this->Plugin->create([
            'Plugin' => [
                'name' => '123456789012345678901234567890123456789012345678901',
                'title' => '123456789012345678901234567890123456789012345678901',
            ]
        ]);
        $this->assertFalse($this->Plugin->validates());
        $this->assertArrayHasKey('name', $this->Plugin->validationErrors);
        $this->assertEquals('プラグイン名は50文字以内としてください。', current($this->Plugin->validationErrors['name']));
        $this->assertArrayHasKey('title', $this->Plugin->validationErrors);
        $this->assertEquals('プラグインタイトルは50文字以内とします。', current($this->Plugin->validationErrors['title']));
    }

    public function test半角英数チェック異常系()
    {
        $this->Plugin->create([
            'Plugin' => [
                'name' => '１２３ａｂｃ',
            ]
        ]);
        $this->assertFalse($this->Plugin->validates());
        $this->assertArrayHasKey('name', $this->Plugin->validationErrors);
        $this->assertEquals('プラグイン名は半角英数字、ハイフン、アンダースコアのみが利用可能です。', current($this->Plugin->validationErrors['name']));
    }

    public function test重複チェック異常系()
    {
        $this->Plugin->create([
            'Plugin' => [
                'name' => 'BcBlog',
            ]
        ]);
        $this->assertFalse($this->Plugin->validates());
        $this->assertArrayHasKey('name', $this->Plugin->validationErrors);
        $this->assertEquals('指定のプラグインは既に使用されています。', current($this->Plugin->validationErrors['name']));
    }

}
