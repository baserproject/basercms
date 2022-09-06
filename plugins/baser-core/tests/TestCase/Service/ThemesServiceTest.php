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

use BaserCore\Service\ThemesService;
use BaserCore\Service\ThemesServiceInterface;
use BaserCore\Utility\BcContainerTrait;

/**
 * ThemesServiceTest
 * @property ThemesService $ThemesService
 */
class ThemesServiceTest extends \BaserCore\TestSuite\BcTestCase
{

    /**
     * Trait
     */
    use BcContainerTrait;

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->ThemesService = $this->getService(ThemesServiceInterface::class);
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
     */
    public function testGetDefaultDataPatterns()
    {
        $options = ['useTitle' => false];
        $result = $this->ThemesService->getDefaultDataPatterns('BcFront', $options);
        $expected = [
            'BcFront.default' => 'default',
            'BcFront.empty' => 'empty'
        ];
        $this->assertEquals($expected, $result, '初期データのセットのタイトルを外して取得できません');
        $result = $this->ThemesService->getDefaultDataPatterns('BcFront');
        $expected = [
            'BcFront.default' => 'フロントテーマ ( default )',
            'BcFront.empty' => 'フロントテーマ ( empty )'
        ];
        $this->assertEquals($expected, $result);
    }

    /**
     * test delete
     * @return void
     */
    public function testDelete()
    {
        $this->ThemesService->copy('BcFront');
        $rs = $this->ThemesService->delete('BcFrontCopy');
        $this->assertTrue($rs);
        $this->assertTrue(!is_dir(BASER_THEMES . 'BcFrontCopy'));
    }
}
