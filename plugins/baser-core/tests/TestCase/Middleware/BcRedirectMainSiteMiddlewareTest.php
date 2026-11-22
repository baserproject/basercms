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

namespace BaserCore\Test\TestCase\Middleware;

use BaserCore\Middleware\BcRedirectMainSiteMiddleware;
use BaserCore\Test\Factory\ContentFactory;
use BaserCore\Test\Factory\PageFactory;
use BaserCore\Test\Factory\SiteFactory;
use BaserCore\Test\Scenario\MultiSiteScenario;
use BaserCore\TestSuite\BcTestCase;
use Cake\Http\Exception\NotFoundException;
use Cake\Http\Response;
use Cake\ORM\TableRegistry;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Class BcRedirectMainSiteMiddlewareTest
 * @property BcRedirectMainSiteMiddleware $BcRedirectMainSiteMiddleware
 */
class BcRedirectMainSiteMiddlewareTest extends BcTestCase
{
    /**
     * Trait
     */
    use ScenarioAwareTrait;

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->BcRedirectMainSiteMiddleware = new BcRedirectMainSiteMiddleware();
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->BcRedirectMainSiteMiddleware);
        parent::tearDown();
    }

    /**
     * test Process
     * サブサイトにコンテンツが無く、メインサイトの同じ URL にコンテンツがある場合はメインサイトへリダイレクトする
     */
    public function test_process(): void
    {
        $this->loadFixtureScenario(MultiSiteScenario::class);
        $this->createMainSitePage();
        $handler = $this->createNotFoundHandler();
        $m = $this->BcRedirectMainSiteMiddleware; $r = $this->getRequest('/en/about');

        $response = $this->BcRedirectMainSiteMiddleware->process($this->getRequest('/en/about'), $handler);
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertEquals('/about', $response->getHeaderLine('Location'));
    }

    /**
     * test Process
     * リダイレクトの際、クエリ文字列を引き継ぐ
     */
    public function test_process_withQuery(): void
    {
        $this->loadFixtureScenario(MultiSiteScenario::class);
        $this->createMainSitePage();

        $response = $this->BcRedirectMainSiteMiddleware->process(
            $this->getRequest('/en/about?page=2&sort=desc'),
            $this->createNotFoundHandler()
        );
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertEquals('/about?page=2&sort=desc', $response->getHeaderLine('Location'));
    }

    /**
     * test Process
     * メインサイトにもコンテンツが無い場合はリダイレクトしない
     */
    public function test_process_notExistsMainSite(): void
    {
        $this->loadFixtureScenario(MultiSiteScenario::class);
        $this->createMainSitePage();
        $this->expectException(NotFoundException::class);
        $this->BcRedirectMainSiteMiddleware->process($this->getRequest('/en/not_exists'), $this->createNotFoundHandler());
    }

    /**
     * test Process
     * サブサイトにコンテンツがある場合（記事が無いなどの 404）はリダイレクトしない
     */
    public function test_process_existsSubSite(): void
    {
        $this->loadFixtureScenario(MultiSiteScenario::class);
        $this->createMainSitePage();
        $this->expectException(NotFoundException::class);
        $this->BcRedirectMainSiteMiddleware->process($this->getRequest('/en/'), $this->createNotFoundHandler());
    }

    /**
     * test Process
     * 404 にならない場合はそのままレスポンスを返す
     */
    public function test_process_found(): void
    {
        $this->loadFixtureScenario(MultiSiteScenario::class);
        $this->createMainSitePage();
        $handler = new class implements RequestHandlerInterface {
            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                return new Response();
            }
        };
        $response = $this->BcRedirectMainSiteMiddleware->process($this->getRequest('/en/about'), $handler);
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * メインサイトに /about の固定ページを作成する
     */
    private function createMainSitePage(): void
    {
        PageFactory::make(['id' => 1])->persist();
        ContentFactory::make([
            'id' => 10,
            'url' => '/about',
            'name' => 'about',
            'plugin' => 'BaserCore',
            'type' => 'Page',
            'site_id' => 1,
            'parent_id' => 1,
            'entity_id' => 1,
            'site_root' => false,
            'status' => true
        ])->persist();
        TableRegistry::getTableLocator()->get('BaserCore.Contents')->recover();
    }

    /**
     * NotFoundException を発生させるハンドラーを作成する
     */
    private function createNotFoundHandler(): RequestHandlerInterface
    {
        return new class implements RequestHandlerInterface {
            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                throw new NotFoundException();
            }
        };
    }
}
