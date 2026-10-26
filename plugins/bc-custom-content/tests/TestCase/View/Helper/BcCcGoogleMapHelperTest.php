<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.4.3
 * @license       https://basercms.net/license/index.html MIT License
 */

namespace BcCustomContent\Test\TestCase\View\Helper;

use BaserCore\Test\Factory\SiteConfigFactory;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\View\BcAdminAppView;
use BcCustomContent\Model\Entity\CustomField;
use BcCustomContent\Model\Entity\CustomLink;
use Cake\ORM\Entity;
use BaserCore\View\BcFrontAppView;
use BcCcGoogleMap\View\Helper\BcCcGoogleMapHelper;
use BcCustomContent\Test\Factory\CustomLinkFactory;

/**
 * BcCcGoogleMapHelper Test Case
 * @property BcCcGoogleMapHelper $BcCcGoogleMapHelper
 */
class BcCcGoogleMapHelperTest extends BcTestCase
{

    private string $c = '35.681236,139.767125';
    private string $g = 'https://www.google.com/maps/@35.681236,139.767125,17z';

    /**
     * setUp
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->BcCcGoogleMapHelper = new BcCcGoogleMapHelper(new BcFrontAppView($this->getRequest()));
    }

    /**
     * tearDown
     */
    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->BcCcGoogleMapHelper);
    }

    public function test_getLatLng()
    {
        $this->assertSame(['lat' => 35.681236, 'lng' => 139.767125], $this->BcCcGoogleMapHelper->getLatLng($this->c));
        $this->assertNull($this->BcCcGoogleMapHelper->getLatLng('abc'));
    }

    public function test_getUrl()
    {
        $h = $this->BcCcGoogleMapHelper;
        $this->assertSame('https://www.google.com/maps/search/?api=1&query=35.681236,139.767125', $h->getUrl($this->c));
        $this->assertSame($this->g, $h->getUrl($this->g));
        $this->assertSame('https://www.google.com/maps/search/?api=1&query=35.6,139.7', $h->getUrl('javascript:alert(1)//@35.6,139.7'));
        $this->assertSame('', $h->getUrl(null));
        $this->assertSame('', $h->getUrl('abc'));
    }

    public function test_output_default()
    {
        $h = $this->BcCcGoogleMapHelper;
        $this->assertSame(h($this->g), $h->output($this->g));
        $this->assertSame($this->g, $h->output($this->g, null, ['escape' => false]));
        $this->assertSame('', $h->output(null));
        $this->assertSame(h($this->g), $h->output($this->g, 'unknown'));
    }

    public function test_output_latlng()
    {
        $this->assertSame(['lat' => 35.681236, 'lng' => 139.767125], $this->BcCcGoogleMapHelper->output($this->c, 'latlng'));
        $this->assertNull($this->BcCcGoogleMapHelper->output('', 'latlng'));
    }

    public function test_output_link()
    {
        $h = $this->BcCcGoogleMapHelper;
        $result = $h->output($this->c, 'link');
        $this->assertStringContainsString('href="https://www.google.com/maps/search/?api=1&amp;query=35.681236,139.767125"', $result);
        $this->assertStringContainsString('target="_blank"', $result);
        $this->assertStringContainsString('rel="noopener"', $result);
        $this->assertStringContainsString('>地図を見る</a>', $h->output($this->c, 'link', ['text' => '地図を見る']));
        $this->assertSame('', $h->output('abc', 'link'));
        $this->assertStringNotContainsString('javascript', $h->output('javascript:alert(1)//@35.6,139.7', 'link'));
    }

    public function test_output_embed()
    {
        $h = $this->BcCcGoogleMapHelper;
        $result = $h->output($this->c, 'embed', ['height' => 400, 'zoom' => 12]);
        $this->assertStringContainsString('<iframe', $result);
        $this->assertStringContainsString('q=35.681236,139.767125&amp;z=12&amp;output=embed', $result);
        $this->assertStringContainsString('height="400"', $result);
        $this->assertStringContainsString('width="100%"', $result);
        $this->assertStringContainsString('loading="lazy"', $result);
        $this->assertSame('', $h->output('abc', 'embed'));
        $this->assertSame('', $h->output(null, 'embed'));
    }

    public function test_get()
    {
        $h = $this->BcCcGoogleMapHelper;
        $link = CustomLinkFactory::make(['name' => 'map'])->getEntity();
        $this->assertSame($h->getLatLng($this->c), $h->get($this->c, $link, ['format' => 'latlng']));
        // フロントでは format 未指定時に埋め込み地図を返す（escape の指定は無視する）
        $this->assertStringStartsWith('<iframe', $h->get($this->c, $link));
        $this->assertSame($h->output($this->c, 'embed'), $h->get($this->c, $link));
        $this->assertSame($h->output($this->c, 'embed'), $h->get($this->c, $link, ['escape' => true]));
        $this->assertSame($h->output($this->c, 'link'), $h->get($this->c, $link, ['format' => 'link']));
        // format に null を明示するとURLをエスケープせずに返す
        $this->assertSame($h->getUrl($this->c), $h->get($this->c, $link, ['format' => null]));
        $this->assertSame('', $h->get('abc', $link));
        $this->assertSame('', $h->get(null, $link));

        // 管理画面では format 未指定時に「緯度,経度」を返す（エントリー一覧で再エスケープされるため）
        // getRequest() で現在のリクエストが管理画面に切り替わるため、フロントの確認より後に行う
        $admin = $this->adminHelper();
        $this->assertSame('35.681236,139.767125', $admin->get($this->c, $link));
        $this->assertSame('33.589894,130.391306', $admin->get('<iframe src="https://www.google.com/maps/embed?pb=!1m18!2d130.391306!3d33.589894!2m3"></iframe>', $link));
        $this->assertSame('', $admin->get('abc', $link));
        // 管理画面でも format を明示すればその形式で返す
        $this->assertSame($admin->output($this->c, 'embed'), $admin->get($this->c, $link, ['format' => 'embed']));
    }

    public function test_default_output_is_normalized()
    {
        $h = $this->BcCcGoogleMapHelper;
        $link = CustomLinkFactory::make(['name' => 'map'])->getEntity();
        $js = 'javascript:alert(1)//@35.6,139.7';
        $google = 'https://www.google.com/maps/search/?api=1&query=35.6,139.7';
        $this->assertSame($h->getUrl($this->c), $h->get($this->c, $link, ['format' => 'bogus']));
        $this->assertSame($google, $h->get($js, $link, ['format' => null]));
        $this->assertSame($google, $h->output($js, null, ['escape' => false]));
        $this->assertStringNotContainsString('javascript', $h->get($js, $link));
        $this->assertStringNotContainsString('javascript', $h->get($js, $link, ['format' => 'link']));
        $this->assertSame('', $h->get('"><script>', $link));
        $this->assertSame('', $h->get('"><script>', $link, ['format' => null]));
    }

    /**
     * 管理画面用ヘルパーを作る
     */
    private function adminHelper(): BcCcGoogleMapHelper
    {
        return new BcCcGoogleMapHelper(new BcAdminAppView($this->getRequest('/baser/admin')));
    }

    private function mapLink(): CustomLink
    {
        return new CustomLink(['name' => 'map', 'custom_field' => new CustomField(['placeholder' => '', 'size' => 40])]);
    }

    public function test_control_noApiKey()
    {
        $h = $this->adminHelper();
        $h->BcAdminForm->create();
        $result = $h->control($this->mapLink());
        $this->assertStringContainsString('name="map"', $result);
        $this->assertStringContainsString('bca-cc-google-map', $result);
        $this->assertStringContainsString('35.681236,139.767125', $result);
        $this->assertStringNotContainsString('<button', $result);
        $this->assertStringNotContainsString('data-api-key=', $result);
        $this->assertStringNotContainsString('<iframe', $result);
        $this->assertStringNotContainsString('bca-help', $result);
        // 埋め込みHTMLを貼り付けたときに src だけに置き換えるスクリプトは、APIキーがなくても出力する
        $this->assertSame(1, substr_count($result, '<script'));
        $this->assertStringContainsString("'input'", $result);
        $this->assertStringContainsString('iframe', $result);
        // 正規表現の後方参照がヒアドキュメントで制御文字に変わっていないこと
        $this->assertStringContainsString('(.*?)\\1/i', $result);
        $this->assertStringNotContainsString("\x01", $result);
    }

    /**
     * test getUrl / embed with Google embed iframe
     */
    public function test_googleEmbed()
    {
        $h = $this->BcCcGoogleMapHelper;
        $embedUrl = 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d861.9!2d130.39130656047675!3d33.58989489955554!2m3!1f0!2f0!3f0!5e1!3m2!1sja!2sjp';
        $iframe = '<iframe src="' . $embedUrl . '" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>';
        $this->assertSame(['lat' => 33.58989489955554, 'lng' => 130.39130656047675], $h->getLatLng($iframe));
        $this->assertSame($embedUrl, $h->getUrl($iframe));
        $this->assertSame($embedUrl, $h->getUrl($embedUrl));
        // 埋め込み出力では Google の埋め込みURLをそのまま src に使う（ピンや店名の表示を保つ）
        $embed = $h->output($embedUrl, 'embed', ['height' => 400]);
        $this->assertStringContainsString('src="' . $embedUrl . '"', $embed);
        $this->assertStringNotContainsString('output=embed', $embed);
        $this->assertStringContainsString('height="400"', $embed);
        $this->assertStringContainsString('src="' . $embedUrl . '"', $h->output($iframe, 'embed'));
        // src が javascript: の iframe は、座標から組み立てた Google のURLに置き換える
        $evil = '<iframe src="javascript:alert(1)//!2d139.7!3d35.6"></iframe>';
        $this->assertSame('https://www.google.com/maps/search/?api=1&query=35.6,139.7', $h->getUrl($evil));
        $this->assertStringNotContainsString('javascript', $h->output($evil, 'embed'));
        $this->assertStringNotContainsString('javascript', $h->output($evil, 'link'));
        // Google の埋め込みURL以外は、埋め込み出力で src に使わない
        $other = '<iframe src="https://example.com/map?q=35.6,139.7"></iframe>';
        $this->assertStringContainsString('https://maps.google.com/maps?q=35.6,139.7', $h->output($other, 'embed'));
        $this->assertStringNotContainsString('example.com', $h->output($other, 'embed'));
    }

    public function test_control_withApiKey()
    {
        SiteConfigFactory::make(['name' => 'google_maps_api_key', 'value' => 'test-key'])->persist();
        $h = $this->adminHelper();
        $h->BcAdminForm->create();
        $result = $h->control($this->mapLink());
        $this->assertStringContainsString('bca-cc-google-map__search', $result);
        $this->assertStringContainsString('bca-cc-google-map__address', $result);
        $this->assertStringContainsString('data-api-key="test-key"', $result);
        $this->assertStringContainsString('data-target="map"', $result);
        $this->assertStringContainsString('isComposing', $result);
        $this->assertStringContainsString('229', $result);
    }

    public function test_control_minimap()
    {
        $entity = new Entity(['map' => '35.68,139.76']);
        $entity->setSource('BaserCore.Contents');
        $entity->clean();
        $entity->set('map', 'abc');
        $entity->setNew(false);
        $h = $this->adminHelper();
        $h->BcAdminForm->create($entity);
        $result = $h->control($this->mapLink());
        $this->assertStringContainsString('<iframe', $result);
        $this->assertStringContainsString('q=35.68,139.76', $result);
        $this->assertStringContainsString('height="120"', $result);
        $this->assertStringContainsString('bca-cc-google-map__minimap', $result);
    }

    public function test_control_noMinimapForNewEntity()
    {
        $h = $this->adminHelper();
        $entity = new Entity([]);
        $entity->setSource('BaserCore.Contents');
        $h->BcAdminForm->create($entity);
        $this->assertStringNotContainsString('<iframe', $h->control($this->mapLink()));
    }

    public function test_control_noMinimapForNewCustomEntry()
    {
        $h = $this->adminHelper();
        $h->BcAdminForm->create(new \BcCustomContent\Model\Entity\CustomEntry(['map' => '35.68,139.76']));
        $this->assertStringNotContainsString('<iframe', $h->control($this->mapLink()));
    }

    public function test_control_withFormProtection()
    {
        SiteConfigFactory::make(['name' => 'google_maps_api_key', 'value' => 'test-key'])->persist();
        $view = new BcAdminAppView($this->getRequest('/baser/admin')->withAttribute('formTokenData', [
            'unlockedFields' => [],
        ]));
        $h = new BcCcGoogleMapHelper($view);
        $entity = new Entity(['map' => '35.68,139.76']);
        $entity->setSource('BaserCore.Contents');
        $h->BcAdminForm->create($entity);
        $result = $h->control($this->mapLink());
        $this->assertStringContainsString('bca-cc-google-map__address', $result);
        $this->assertStringNotContainsString('name="bca_cc', $result);
        $this->assertStringContainsString("'keydown'", $result);
    }

    public function test_control_scriptOnce()
    {
        SiteConfigFactory::make(['name' => 'google_maps_api_key', 'value' => 'test-key'])->persist();
        $h = $this->adminHelper();
        $h->BcAdminForm->create();
        $result = $h->control($this->mapLink()) . $h->control($this->mapLink());
        $this->assertSame(1, substr_count($result, '<script'));
    }

    public function test_preview_searchControl()
    {
        $h = $this->adminHelper();
        $h->BcAdminForm->create();
        $preview = $h->preview($this->mapLink());
        $this->assertStringContainsString(':placeholder="entity.placeholder"', $preview);
        $this->assertStringContainsString(':value="entity.default_value"', $preview);
        $this->assertStringNotContainsString('bca-cc-google-map', $preview);
        $search = $h->searchControl($this->mapLink());
        $this->assertStringContainsString('name="map"', $search);
        $this->assertStringNotContainsString('bca-cc-google-map', $search);
        $this->assertStringNotContainsString('<iframe', $search);
    }

    public function test_helpText()
    {
        $result = $this->adminHelper()->helpText($this->mapLink());
        $this->assertStringContainsString('座標', $result);
        $this->assertStringContainsString('maps.app.goo.gl', $result);
        $this->assertStringNotContainsString('<i class="bca-icon', $result);
        $this->assertStringContainsString('地図を埋め込む', $result);
    }

}
