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
        $configPath = BcUtil::getPluginPath($pluginName) . 'config.php';
        if ($configContent !== null) {
            file_put_contents($configPath, $configContent);
        }

        $result = BcPluginUtil::getPluginConfig($pluginName);

        $this->assertEquals($expected, $result);

        //clean up
        if ($configContent !== null) {
            unlink($configPath);
        }
    }

    public static function getPluginConfigDataProvider()
    {
        return [
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
}
