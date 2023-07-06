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

namespace BcWidgetArea\Test\TestCase\Service\Admin;

use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcUtil;
use BcWidgetArea\Model\Entity\WidgetArea;
use BcWidgetArea\Service\Admin\WidgetAreasAdminService;
use BcWidgetArea\Service\Admin\WidgetAreasAdminServiceInterface;

/**
 * WidgetAreasAdminServiceTest
 * @property WidgetAreasAdminService $WidgetAreasAdminService
 */
class WidgetAreasAdminServiceTest extends BcTestCase
{

    /**
     * set up
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->WidgetAreasAdminService = $this->getService(WidgetAreasAdminServiceInterface::class);
    }

    /**
     * tear down
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * test getViewVarsForEdit
     */
    public function test_getViewVarsForEdit()
    {
        $entity = new WidgetArea();
        $result = $this->WidgetAreasAdminService->getViewVarsForEdit($entity);
        $this->assertInstanceOf(WidgetArea::class, $result['widgetArea']);
        $this->assertEquals([
            'title' => __d('baser_core', 'コアウィジェット'),
            'plugin' => 'BaserCore',
            'paths' => ['/var/www/html/plugins/bc-admin-third/templates/Admin/element/widget']
        ], $result['widgetInfos'][0]);

    }

    /**
     * test getWidgetInfos
     */
    public function test_getWidgetInfos()
    {

    }


}
