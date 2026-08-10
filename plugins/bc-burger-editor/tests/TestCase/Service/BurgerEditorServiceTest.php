<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.4.0
 * @license       https://basercms.net/license/index.html MIT License
 */

namespace BcBurgerEditor\Test\TestCase\Service;

use BaserCore\TestSuite\BcTestCase;
use BcBurgerEditor\Service\BurgerEditorService;
use Cake\Core\Plugin;

/**
 * BurgerEditorServiceTest
 */
class BurgerEditorServiceTest extends BcTestCase
{

    /**
     * @var BurgerEditorService
     */
    public $BurgerEditorService;

    /**
     * 一時ファイルの配置先
     *
     * @var string
     */
    private $tmpDir;

    /**
     * setUp
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->BurgerEditorService = new BurgerEditorService();
        $this->tmpDir = TMP . 'bc_burger_editor_test' . DS;
        if (!is_dir($this->tmpDir)) {
            mkdir($this->tmpDir, 0777, true);
        }
    }

    /**
     * tearDown
     */
    public function tearDown(): void
    {
        foreach(glob($this->tmpDir . '*') as $file) {
            if (is_file($file)) unlink($file);
        }
        if (is_dir($this->tmpDir)) rmdir($this->tmpDir);
        unset($this->BurgerEditorService);
        parent::tearDown();
    }

    /**
     * test rotateImage
     *
     * Exif を持たない画像は回転せずそのまま残る
     */
    public function test_rotateImage_withoutExif()
    {
        $source = Plugin::path('BcBurgerEditor') . 'webroot' . DS . 'img' . DS . 'bg-sample.png';
        $target = $this->tmpDir . 'sample.png';
        copy($source, $target);
        $before = md5_file($target);

        $this->assertTrue($this->BurgerEditorService->rotateImage($target));
        $this->assertSame($before, md5_file($target), '回転対象外の画像が書き換えられています');
    }

    /**
     * test rotateImage
     *
     * 画像以外のファイルを渡しても例外とならない
     */
    public function test_rotateImage_withNotImageFile()
    {
        $target = $this->tmpDir . 'sample.txt';
        file_put_contents($target, 'this is not an image');
        $this->assertTrue($this->BurgerEditorService->rotateImage($target));
    }

    /**
     * test rotateImage
     *
     * 存在しないファイルを渡しても例外とならない
     */
    public function test_rotateImage_withMissingFile()
    {
        $this->assertTrue($this->BurgerEditorService->rotateImage($this->tmpDir . 'not-exists.jpg'));
    }

    /**
     * test getBasePath
     *
     * プラグインの基準パスを返す
     */
    public function test_getBasePath()
    {
        $result = $this->BurgerEditorService->getBasePath();
        $this->assertSame(Plugin::path('BcBurgerEditor'), $result);
        $this->assertDirectoryExists($result);
    }

    /**
     * test getBlockPath
     *
     * 実在するブロックの基準パスを返す
     */
    public function test_getBlockPath()
    {
        $result = $this->BurgerEditorService->getBlockPath();
        $this->assertSame(Plugin::path('BcBurgerEditor') . 'Addon' . DS . 'block' . DS, $result);
        $this->assertDirectoryExists($result);
    }

    /**
     * test getTypePath
     *
     * 実在するタイプの基準パスを返す
     */
    public function test_getTypePath()
    {
        $result = $this->BurgerEditorService->getTypePath();
        $this->assertSame(Plugin::path('BcBurgerEditor') . 'Addon' . DS . 'type' . DS, $result);
        $this->assertDirectoryExists($result);
    }

    /**
     * test element
     *
     * タイプの表示用テンプレートが出力される
     */
    public function test_element()
    {
        ob_start();
        $this->BurgerEditorService->element('embed');
        $result = ob_get_clean();
        $this->assertStringContainsString('<div class="valueembed">', $result);
        $this->assertContains('embed', BurgerEditorService::getAddonList());
    }

    /**
     * test element
     *
     * 存在しないタイプを指定しても何も出力されない
     */
    public function test_element_withMissingType()
    {
        ob_start();
        $this->BurgerEditorService->element('not-exists-type');
        $result = ob_get_clean();
        $this->assertSame('', $result);
    }

    /**
     * test addAddonList / getAddonList
     *
     * 同じ名前は重複して登録されない
     */
    public function test_getAddonList()
    {
        ob_start();
        $this->BurgerEditorService->element('embed');
        $this->BurgerEditorService->element('embed');
        ob_end_clean();
        $result = BurgerEditorService::getAddonList();
        $this->assertSame(1, count(array_keys($result, 'embed')));
    }

}
