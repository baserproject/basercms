<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @since           baserCMS v 3.0.0
 * @license         https://basercms.net/license/index.html
 */

namespace BcFavorite\Test\TestCase\Event;

use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\View\BcAdminAppView;
use BcFavorite\Event\BcFavoriteViewEventListener;
use Cake\Event\Event;
use Cake\View\View;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * Class BcFavoriteViewEventListener
 * @property BcFavoriteViewEventListener $BcFavoriteViewEventListener
 */
class BcFavoriteViewEventListenerTest extends BcTestCase
{

    /**
     * Trait
     */
    use ScenarioAwareTrait;

    /**
     * Setup
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->BcFavoriteViewEventListener = new BcFavoriteViewEventListener("view");
    }

    /**
     * Tear down
     *
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * Test beforeAdminMenu
     *
     * @return void
     */
    public function testBeforeAdminMenu(): void
    {
        $this->loadFixtureScenario(InitAppScenario::class);

        //ログインしない、又はadminではない場合、nullを返す
        $this->assertNull($this->BcFavoriteViewEventListener->beforeAdminMenu(new Event('beforeAdminMenu', new View())));

        //adminでログインした場合、
        $this->loginAdmin($this->getRequest('/baser/admin'));
        ob_start();
        $this->BcFavoriteViewEventListener->beforeAdminMenu(new Event('beforeAdminMenu', new BcAdminAppView()));
        $actual = ob_get_clean();
        $this->assertStringContainsString("お気に入り名", $actual);
    }

    /**
     * Test beforeContentsMenu
     *
     * @return void
     */
    public function testBeforeContentsMenu(): void
    {
        $this->loadFixtureScenario(InitAppScenario::class);

        //ログインしない、又はadminではない場合、nullを返す
        $this->assertNull($this->BcFavoriteViewEventListener->beforeContentsMenu(new Event('beforeContentsMenu', new View())));

        //adminでログインした場合、
        $this->loginAdmin($this->getRequest('/baser/admin'));
        $event = new Event('beforeContentsMenu', new BcAdminAppView());
        $this->BcFavoriteViewEventListener->beforeContentsMenu($event);

        //$eventにデータがセットできるか確認すること
        $this->assertEquals(
            ['<a href="javascript:void(0)" id="BtnFavoriteAdd" data-bca-fn="BtnFavoriteAdd" class="bca-content-menu__link bca-icon--plus-square">お気に入りに追加</a>'],
            $event->getData("contentsMenu"));
    }
}
