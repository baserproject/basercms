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

namespace BcUploader\Test\TestCase\Controller;

use BaserCore\Test\Factory\SiteFactory;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcFile;
use BcUploader\Test\Factory\UploaderFileFactory;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;
use Cake\TestSuite\IntegrationTestTrait;

/**
 * Class UploaderFilesControllerTest
 */
class UploaderFilesControllerTest extends BcTestCase
{

    /**
     * ScenarioAwareTrait
     */
    use ScenarioAwareTrait;
    use IntegrationTestTrait;

    /**
     * set up
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
     * test view_limited_file
     * @return void
     */
    public function test_view_limited_file()
    {
        SiteFactory::make()->main()->persist();
        UploaderFileFactory::make(['name' => '2_2_test.jpg', 'publish_end' => '2000-01-27 12:00:00'])->persist();
        UploaderFileFactory::make(['name' => '2_3_test.jpg', 'publish_begin' => '2000-01-27 12:00:00'])->persist();
        $file = new BcFile(WWW_ROOT . 'files' . DS . 'uploads' . DS . 'limited' . DS . '2_3_test.jpg');
        $file->create();

        //ログインしていない、かつ　未公開状態　場合：404を返す
        $this->get("/files/uploads/2_2_test.jpg");
        $this->assertResponseCode(404);

        //ログインしていない、かつ　公開状態　場合：
        $this->get("/files/uploads/2_3_test.jpg");
        $this->assertResponseCode(200);
        $this->assertEquals(["image/jpeg"], $this->_response->getHeader("Content-type"));
        $this->assertNotEmpty($this->_response->getBody());

        //不要ファイルを削除
        $file->delete();
    }
}
