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

namespace BaserCore\Test\TestCase\Service;

/**
 * ThemesServiceTest
 */
class ThemesServiceTest extends \BaserCore\TestSuite\BcTestCase
{

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * 初期データのセットを取得する
     *
     * @param string $theme
     * @param array $options
     * @return array
     */
    public function testGetDefaultDataPatterns()
    {
        $options = ['useTitle' => false];
        $result = $this->BcManager->getDefaultDataPatterns('core', $options);
        $expected = [
            'core.default' => 'default'
        ];
        $this->assertEquals($expected, $result, '初期データのセットのタイトルを外して取得できません');
    }

}
