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

namespace BcCcGoogleMap\View\Helper;

use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\View\Helper\BcAdminFormHelper;
use BcCcGoogleMap\Utility\BcCcGoogleMapUtil;
use BcCustomContent\Model\Entity\CustomLink;
use BaserCore\Utility\BcSiteConfig;
use BaserCore\Utility\BcUtil;
use Cake\Utility\Text;
use Cake\View\Helper;
use Cake\View\Helper\HtmlHelper;

/**
 * Class BcCcGoogleMapHelper
 *
 * @property BcAdminFormHelper $BcAdminForm
 * @property HtmlHelper $Html
 */
#[\AllowDynamicProperties]
class BcCcGoogleMapHelper extends Helper
{

    /**
     * 画面用 JS を出力済みか
     * @var bool
     */
    private bool $scriptRendered = false;

    /**
     * Helper
     * @var array
     */
    public array $helpers = [
        'BaserCore.BcAdminForm' => ['templates' => 'BaserCore.bc_form'],
        'Html',
    ];

    /**
     * 値から緯度経度を取得する
     *
     * @param string|null $value
     * @return array|null ['lat' => float, 'lng' => float]
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getLatLng(?string $value): ?array
    {
        return BcCcGoogleMapUtil::parse($value);
    }

    /**
     * 値から地図のURLを取得する
     *
     * http(s) で始まり座標を抽出できる値はそのまま返す。
     * それ以外で座標を抽出できる場合は、座標から組み立てたURLを返す。
     *
     * @param string|null $value
     * @return string
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getUrl(?string $value): string
    {
        $latLng = $this->getLatLng($value);
        if (!$latLng) return '';
        $url = (string)BcCcGoogleMapUtil::extractUrl($value);
        if (preg_match('/^https?:\/\//i', $url)) {
            return $url;
        }
        return 'https://www.google.com/maps/search/?api=1&query=' . $this->formatLatLng($latLng);
    }

    /**
     * 値を指定の形式で出力する
     *
     * @param string|null $value
     * @param string|null $format null|'latlng'|'link'|'embed'
     * @param array $options
     * @return mixed
     * @checked
     * @noTodo
     * @unitTest
     */
    public function output(?string $value, ?string $format = null, array $options = []): mixed
    {
        switch ($format) {
            case 'latlng':
                return $this->getLatLng($value);
            case 'link':
                $url = $this->getUrl($value);
                if ($url === '') return '';
                return $this->Html->link($options['text'] ?? $url, $url, [
                    'target' => '_blank',
                    'rel' => 'noopener',
                ]);
            case 'embed':
                $latLng = $this->getLatLng($value);
                if (!$latLng) return '';
                $url = BcCcGoogleMapUtil::extractUrl($value);
                $zoom = (int)($options['zoom'] ?? 15);
                // Google の埋め込みURLはそのまま使い、ピンや店名の表示を保つ
                $src = BcCcGoogleMapUtil::isGoogleEmbedUrl($url)
                    ? $url
                    : 'https://maps.google.com/maps?q=' . $this->formatLatLng($latLng) . '&z=' . $zoom . '&output=embed';
                $attributes = [];
                if (!empty($options['class'])) {
                    $attributes['class'] = $options['class'];
                }
                return $this->Html->tag('iframe', '', $attributes + [
                    'src' => $src,
                    'width' => $options['width'] ?? '100%',
                    'height' => $options['height'] ?? 300,
                    'loading' => 'lazy',
                    'style' => 'border:0',
                    'referrerpolicy' => 'no-referrer-when-downgrade',
                    'allowfullscreen' => true,
                ]);
            default:
                $url = $this->getUrl($value);
                if (isset($options['escape']) && $options['escape'] === false) {
                    return $url;
                }
                return h($url);
        }
    }

    /**
     * Get
     *
     * `format` を指定しない場合は埋め込み地図（iframe）を返す。
     * ただし管理画面では、エントリー一覧でさらにエスケープされるため「緯度,経度」の文字列を返す。
     * getFieldValue() は常に escape => true を渡すため、ここでは escape => false に固定し、
     * strip_tags() で <a> と <iframe> 以外のタグを取り除いて返す。
     *
     * @param mixed $fieldValue
     * @param CustomLink $link
     * @param array $options
     * @return mixed
     * @checked
     * @noTodo
     * @unitTest
     */
    public function get($fieldValue, CustomLink $link, array $options = [])
    {
        if (!array_key_exists('format', $options) && BcUtil::isAdminSystem()) {
            $latLng = $this->getLatLng($fieldValue === null ? null : (string)$fieldValue);
            return $latLng ? $this->formatLatLng($latLng) : '';
        }
        $options = array_merge(['format' => 'embed'], $options, ['escape' => false]);
        $out = $this->output($fieldValue === null ? null : (string)$fieldValue, $options['format'], $options);
        if (!is_string($out)) return $out;
        return strip_tags($out, '<a><iframe>');
    }

    /**
     * コントロール
     *
     * @param CustomLink $link
     * @param array $options
     * @return string
     * @checked
     * @noTodo
     * @unitTest
     */
    public function control(CustomLink $link, array $options = []): string
    {
        $field = $link->custom_field;
        $options = array_merge([
            'type' => 'text',
            'size' => $field->size ?? null,
            'maxlength' => $field->max_length ?? null,
            'placeholder' => !empty($field->placeholder) ? $field->placeholder : '35.681236,139.767125',
        ], $options);
        $input = $this->BcAdminForm->control($link->name, $options);

        $apiKey = (string)BcSiteConfig::get('google_maps_api_key');
        $search = '';
        if ($apiKey !== '') {
            $search = $this->Html->div('bca-cc-google-map__search-box',
                $this->Html->tag('input', null, [
                    'type' => 'text',
                    'class' => 'bca-cc-google-map__address bca-textbox__input',
                    'placeholder' => __d('baser_core', '住所'),
                    'size' => 30,
                ]) . ' ' .
                $this->Html->tag('button', __d('baser_core', '住所から検索'), [
                    'type' => 'button',
                    'class' => 'bca-cc-google-map__search bca-btn',
                    'data-api-key' => $apiKey,
                ])
            );
        }

        $minimap = '';
        $context = $this->BcAdminForm->context();
        $entity = method_exists($context, 'entity') ? $context->entity() : null;
        if ($entity && method_exists($entity, 'getOriginal') && !$entity->isNew()) {
            $original = $entity->getOriginal($link->name);
            if (is_string($original) && $this->getLatLng($original)) {
                $minimap = $this->Html->div('bca-cc-google-map__minimap-box',
                    $this->output($original, 'embed', ['height' => 120, 'class' => 'bca-cc-google-map__minimap'])
                );
            }
        }

        $targetId = mb_strtolower(Text::slug($link->name, '-'));
        return $this->Html->div('bca-cc-google-map', $input . $search . $minimap, ['data-target' => $targetId]) .
            $this->renderScript();
    }

    /**
     * プレビュー
     *
     * @param CustomLink $link
     * @return string
     * @checked
     * @noTodo
     * @unitTest
     */
    public function preview(CustomLink $link): string
    {
        return $this->BcAdminForm->control($link->name, [
            'type' => 'text',
            ':size' => 'entity.size',
            ':maxlength' => 'entity.max_length',
            ':placeholder' => 'entity.placeholder',
            ':value' => 'entity.default_value',
        ]);
    }

    /**
     * 検索コントロール
     *
     * @param CustomLink $link
     * @param array $options
     * @return string
     * @checked
     * @noTodo
     * @unitTest
     */
    public function searchControl(CustomLink $link, array $options = []): string
    {
        return $this->BcAdminForm->control($link->name, array_merge([
            'type' => 'text',
            'size' => '',
            'maxlength' => '',
            'placeholder' => '',
        ], $options));
    }

    /**
     * 補足ヘルプの HTML を返す
     *
     * @param CustomLink $link
     * @return string
     * @checked
     * @noTodo
     * @unitTest
     */
    public function helpText(CustomLink $link): string
    {
        return implode('<br>', [
            h(__d('baser_core', '緯度,経度（例：35.681236,139.767125）、または座標を含む地図のURL（Google 以外でも可）を入力できます。')),
            h(__d('baser_core', '短縮URL（maps.app.goo.gl など）は座標を取り出せないため使えません。')),
            h(__d('baser_core', '座標の調べ方：Google マップで地点を右クリックし、先頭に表示される座標をクリックするとコピーできます。')),
            h(__d('baser_core', 'Google マップの「共有」→「地図を埋め込む」でコピーした HTML をそのまま貼り付けても使えます（URL部分だけが保存されます）。')),
        ]);
    }

    /**
     * 画面用 JS を出力する（1インスタンスにつき1回）
     *
     * @return string
     */
    private function renderScript(): string
    {
        if ($this->scriptRendered) return '';
        $this->scriptRendered = true;
        $message = json_encode(__d('baser_core', '住所から座標を取得できませんでした'), JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP);
        $script = <<<JS
(() => {
    let loader = null;
    const load = (key) => {
        if (window.google && window.google.maps) return Promise.resolve();
        if (!loader) {
            loader = new Promise((resolve, reject) => {
                const s = document.createElement('script');
                s.src = 'https://maps.google.com/maps/api/js?key=' + encodeURIComponent(key);
                s.onload = () => resolve();
                s.onerror = () => { loader = null; reject(new Error('load')); };
                document.head.appendChild(s);
            });
        }
        return loader;
    };
    document.addEventListener('input', (e) => {
        const wrap = e.target.closest ? e.target.closest('.bca-cc-google-map') : null;
        if (!wrap || e.target.id !== wrap.dataset.target) return;
        const m = e.target.value.match(/<\s*iframe\b[^>]*\ssrc\s*=\s*(["'])(.*?)\\1/i);
        if (!m) return;
        const decoder = document.createElement('textarea');
        decoder.innerHTML = m[2];
        e.target.value = decoder.value.trim();
    });
    document.addEventListener('keydown', (e) => {
        if (e.isComposing || e.keyCode === 229) return;
        if (e.key !== 'Enter' || !e.target.closest || !e.target.closest('.bca-cc-google-map__address')) return;
        e.preventDefault();
        const search = e.target.closest('.bca-cc-google-map').querySelector('.bca-cc-google-map__search');
        if (search) search.click();
    });
    document.addEventListener('click', (e) => {
        const button = e.target.closest('.bca-cc-google-map__search');
        if (!button) return;
        e.preventDefault();
        const wrap = button.closest('.bca-cc-google-map');
        const address = wrap.querySelector('.bca-cc-google-map__address').value;
        const target = document.getElementById(wrap.dataset.target);
        const fail = () => alert({$message});
        if (!address || !target) { fail(); return; }
        load(button.dataset.apiKey).then(() => {
            new google.maps.Geocoder().geocode({address: address}, (results, status) => {
                if (status !== 'OK' || !results || !results[0]) { fail(); return; }
                const l = results[0].geometry.location;
                target.value = l.lat().toFixed(6) + ',' + l.lng().toFixed(6);
            });
        }).catch(fail);
    });
})();
JS;
        return $this->Html->scriptBlock($script);
    }

    /**
     * 緯度経度を URL 用の文字列にする
     *
     * @param array $latLng
     * @return string
     */
    private function formatLatLng(array $latLng): string
    {
        return $latLng['lat'] . ',' . $latLng['lng'];
    }

}
