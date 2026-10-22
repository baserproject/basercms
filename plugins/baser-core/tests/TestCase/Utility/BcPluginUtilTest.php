<?php

namespace BaserCore\Test\TestCase\Utility;

use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcPluginUtil;
use BaserCore\Utility\BcUtil;

class BcPluginUtilTest extends BcTestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * test getPluginConfig
     * @param string $pluginName
     * @param string|null $configContent
     * @param array $expected
     * @dataProvider getPluginConfigDataProvider
     */
    public function testGetPluginConfig($pluginName, $configContent, $expected)
    {
        //create config file
        $this->createPluginConfig($pluginName, $configContent);

        $result = BcPluginUtil::getPluginConfig($pluginName);

        $this->assertEquals($expected, $result);

        //clean up
        $this->deletePluginConfig($pluginName, $configContent);
    }

    /**
     * テスト用にプラグインディレクトリと config.php を作成する
     *
     * プラグイン名として妥当でない名前（.. 等）はディレクトリを作成しない
     */
    private function createPluginConfig(string $pluginName, ?string $configContent): void
    {
        if ($configContent === null) return;
        if (!preg_match('/^[A-Za-z0-9][A-Za-z0-9_\-]*$/', $pluginName)) return;
        $path = ROOT . DS . 'plugins' . DS . $pluginName;
        if (!is_dir($path)) mkdir($path, 0777, true);
        file_put_contents($path . DS . 'config.php', $configContent);
    }

    /**
     * テスト用に作成したプラグインディレクトリを削除する
     */
    private function deletePluginConfig(string $pluginName, ?string $configContent): void
    {
        if ($configContent === null) return;
        if (!preg_match('/^[A-Za-z0-9][A-Za-z0-9_\-]*$/', $pluginName)) return;
        $path = ROOT . DS . 'plugins' . DS . $pluginName;
        if (file_exists($path . DS . 'config.php')) unlink($path . DS . 'config.php');
        if (is_dir($path)) rmdir($path);
    }

    public static function getPluginConfigDataProvider()
    {
        return [
            // プラグイン名として妥当でない名前は config.php を読み込まない
            [
                '..',
                '<?php return [\'type\' => \'Plugin\', \'title\' => \'Evil\'];',
                [
                    'type' => [],
                    'title' => '',
                    'description' => '',
                    'author' => '',
                    'url' => '',
                    'adminLink' => '',
                    'installMessage' => '',
                ]
            ],
            [
                'non_existent_plugin',
                null,
                [
                    'type' => [],
                    'title' => '',
                    'description' => '',
                    'author' => '',
                    'url' => '',
                    'adminLink' => '',
                    'installMessage' => '',
                ]
            ],

            [
                'test_plugin',
                '<?php return [\'type\' => \'Plugin\', \'title\' => \'Test Plugin\'];',
                [
                    'type' => ['Plugin'],
                    'title' => 'Test Plugin',
                    'description' => '',
                    'author' => '',
                    'url' => '',
                    'adminLink' => '',
                    'installMessage' => '',
                ]
            ],

            [
                'test_plugin',
                '<?php return [\'type\' => \'Plugin,CorePlugin\', \'title\' => \'Test Plugin\'];',
                [
                    'type' => ['Plugin', 'CorePlugin'],
                    'title' => 'Test Plugin',
                    'description' => '',
                    'author' => '',
                    'url' => '',
                    'adminLink' => '',
                    'installMessage' => '',
                ]
            ]
        ];
    }

    /**
     * test isPlugin
     * @param string $pluginName
     * @param string|null $configContent
     * @param bool $expectedResult
     * @dataProvider isPluginDataProvider
     */
    public function testIsPlugin($pluginName, $configContent, $expectedResult)
    {
        //create config file
        $this->createPluginConfig($pluginName, $configContent);

        $result = BcPluginUtil::isPlugin($pluginName);

        $this->assertEquals($expectedResult, $result);

        // Clean up
        $this->deletePluginConfig($pluginName, $configContent);
    }

    public static function isPluginDataProvider()
    {
        return [
            ['BaserCore', null, true],
            ['test_plugin', '<?php return [\'type\' => \'Plugin\'];', true],
            ['core_plugin', '<?php return [\'type\' => \'CorePlugin\'];', true],
            ['non_plugin', '<?php return [\'type\' => \'SomeOtherType\'];', false],
        ];
    }

    /**
     * Test isTheme
     * @param string $pluginName
     * @param string|null $configContent
     * @param bool $expected
     * @dataProvider isThemeDataProvider
     */
    public function testIsTheme($pluginName, $configContent, $expected)
    {
        //Create config file
        $this->createPluginConfig($pluginName, $configContent);

        $result = BcPluginUtil::isTheme($pluginName);
        $this->assertEquals($expected, $result);

        //clean up
        $this->deletePluginConfig($pluginName, $configContent);
    }
    public static function isThemeDataProvider()
    {
        return [
            ['test_theme', '<?php return [\'type\' => \'Theme\'];', true],
            ['admin_theme', '<?php return [\'type\' => \'AdminTheme\'];', true],
            ['non_theme', '<?php return [\'type\' => \'SomeOtherType\'];', false],
            ['non_existent_theme', null, false],
        ];
    }
}
