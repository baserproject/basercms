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
     * test addAddonList / getAddonList
     *
     * 同じ名前は重複して登録されない
     */
    public function test_getAddonList()
    {
        $before = BurgerEditorService::getAddonList();
        $this->assertIsArray($before);
    }

}
