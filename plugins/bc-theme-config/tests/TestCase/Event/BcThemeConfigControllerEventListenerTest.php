<?php

namespace BcThemeConfig\Test\TestCase\Event;

use BaserCore\TestSuite\BcTestCase;
use BcThemeConfig\Event\BcThemeConfigControllerEventListener;
use Cake\Event\Event;

class BcThemeConfigControllerEventListenerTest extends BcTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->BcThemeConfigControllerEventListener = new BcThemeConfigControllerEventListener();
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * Test baserCoreThemesAfterApply
     */
    public function test_baserCoreThemesAfterApply()
    {
        $path = WWW_ROOT . 'files' . DS . 'theme_configs' . DS . 'config.css';

        if (!is_dir(dirname($path))) {
            mkdir(dirname($path), 0777, true);
        }
        file_put_contents($path, 'test content');
        $this->assertFileExists($path);

        $event = new Event('BaserCore.Themes.afterApply');
        $this->BcThemeConfigControllerEventListener->baserCoreThemesAfterApply($event);

        $this->assertFileDoesNotExist($path);
    }
}
