<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */

namespace BaserCore\Test\TestCase\Service\Admin;

use BaserCore\Service\Admin\ContentManageService;
use BaserCore\TestSuite\BcTestCase;

/**
 * ContentManageServiceTest
 * @property ContentManageService $ContentManage
 */
class ContentManageServiceTest extends BcTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
     protected $fixtures = [
          'plugin.BaserCore.Sites',
          'plugin.BaserCore.Contents'
     ];

     /**
      * setUp
      */
     public function setUp():void
     {
          parent::setUp();
          $this->ContentManage = new ContentManageService();
     }

     /**
      * tearDown
      */
     public function tearDown(): void
     {
          parent::tearDown();
          unset($this->ContentManage);
     }

     /**
      * test getContensInfo
      */
     public function testGetContentsInfo()
     {
          $result = $this->ContentManage->getContensInfo();
          $this->assertTrue(isset($result[0]['unpublished']));
          $this->assertTrue(isset($result[0]['published']));
          $this->assertTrue(isset($result[0]['total']));
          $this->assertTrue(isset($result[0]['display_name']));
     }

}
