<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.2.0
 * @license       https://basercms.net/license/index.html MIT License
 */

namespace BcSeo\Test\TestCase\Controller\Admin;

use BaserCore\Service\BcDatabaseServiceInterface;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Test\Scenario\InitAppScenario;
use Cake\Core\Configure;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * SeoConfigsControllerTest
 */
class SeoConfigsControllerTest extends BcTestCase
{
    use ScenarioAwareTrait;

    /**
     * setUp
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->loadFixtureScenario(InitAppScenario::class);
    }

    /**
     * testUpdate_db
     */
    public function testUpdate_db()
    {
        $bcDatabaseService = $this->getService(BcDatabaseServiceInterface::class);

        $fields = Configure::read('BcSeo.fields');
        $fields['test'] = [
            'title' => 'test',
            'type' => 'text',
        ];
        Configure::write('BcSeo.fields', $fields);

        // カラム追加
        $this->loginAdmin($this->getRequest());
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        $this->post('/baser/admin/bc-seo/seo_configs/update_db');
        $this->assertResponseOk();
        $this->assertTrue($bcDatabaseService->columnExists('seo_metas', 'test'));

        // カラム削除
        $bcDatabaseService->removeColumn('seo_metas', 'test');
    }
}
