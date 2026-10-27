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

namespace BcCcGoogleMap\Utility;

use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * BcCcGoogleMapUtil
 *
 * 入力値（座標、または座標を含むURL）から緯度経度を抽出する。
 */
class BcCcGoogleMapUtil
{

    /**
     * 座標の数値パターン
     */
    private const NUM = '(-?\d+(?:\.\d+)?)';

    /**
     * 値から緯度経度を抽出する
     *
     * 空・抽出不可・範囲外の場合は null を返す。
     *
     * @param string|null $value
     * @return array|null ['lat' => float, 'lng' => float]
     * @checked
     * @noTodo
     * @unitTest
     */
    public static function parse(?string $value): ?array
    {
        $value = self::extractUrl($value);
        if ($value === null) return null;
        $value = trim(rawurldecode($value));
        if ($value === '') return null;

        $n = self::NUM;
        // 経度が先に並ぶ形式（Google 埋め込みURL）
        if (!preg_match('/!3d' . $n . '!4d' . $n . '/', $value)
            && preg_match('/!2d' . $n . '!3d' . $n . '/', $value, $m)) {
            return self::build($m[2], $m[1]);
        }
        $patterns = [
            // Google（ピン）
            '/!3d' . $n . '!4d' . $n . '/',
            // Google（表示中心）
            '/@' . $n . ',' . $n . '/',
            // q / query / ll / center / sll
            '/[?&](?:q|query|ll|center|sll)=[\s+]*' . $n . '[\s+]*,[\s+]*' . $n . '(?![\d.])/',
            // OpenStreetMap mlat / mlon（マーカー。表示中心より優先）
            '/[?&]mlat=' . $n . '&(?:amp;)?mlon=' . $n . '/',
            // OpenStreetMap #map=zoom/lat/lng
            '/#map=\d+(?:\.\d+)?\/' . $n . '\/' . $n . '/',
            // Yahoo!マップ lat / lon
            '/[?&]lat=' . $n . '&(?:amp;)?lon=' . $n . '/',
        ];
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $value, $m)) {
                return self::build($m[1], $m[2]);
            }
        }
        // 「数値,数値」だけの文字列
        if (preg_match('/^' . $n . '\s*,\s*' . $n . '$/', $value, $m)) {
            return self::build($m[1], $m[2]);
        }
        return null;
    }

    /**
     * iframe タグが渡された場合は src 属性のURLを取り出す
     *
     * Google マップの「地図を埋め込む」でコピーした HTML をそのまま扱えるようにする。
     * iframe タグでない場合は前後の空白を除いた値を返す。
     *
     * @param string|null $value
     * @return string|null
     * @checked
     * @noTodo
     * @unitTest
     */
    public static function extractUrl(?string $value): ?string
    {
        if ($value === null) return null;
        $value = trim($value);
        if (stripos($value, '<iframe') !== false
            && preg_match('/\ssrc\s*=\s*(["\'])(.*?)\1/is', $value, $m)) {
            return trim(html_entity_decode($m[2], ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        }
        return $value;
    }

    /**
     * Google マップの埋め込み用URLかどうか
     *
     * @param string|null $value
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public static function isGoogleEmbedUrl(?string $value): bool
    {
        return (bool)preg_match('/^https:\/\/www\.google\.com\/maps\/embed\?pb=/i', (string)$value);
    }

    /**
     * 範囲を確認して戻り値を組み立てる
     *
     * @param string $lat
     * @param string $lng
     * @return array|null
     */
    private static function build(string $lat, string $lng): ?array
    {
        $lat = (float)$lat;
        $lng = (float)$lng;
        if ($lat < -90 || $lat > 90 || $lng < -180 || $lng > 180) return null;
        return ['lat' => $lat, 'lng' => $lng];
    }

}
