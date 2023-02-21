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

namespace BcCustomContent\View\Helper;

use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * CustomContentArrayTrait
 */
trait CustomContentArrayTrait {

	/**
	 * 配列とキーを指定して値を取得する
	 * - グループ指定のある配列に対応
	 *
	 * @param int $key
	 * @param array $array
	 * @param string $noValue
	 * @return string
	 */
	public function arrayValue($key, $array, $noValue = '')
	{
		if (is_numeric($key)) {
			$key = (int)$key;
		}
		if (isset($array[$key])) {
			return $array[$key];
		}
		// グループ指定がある場合の判定
		foreach($array as $group => $list) {
			if (is_array($list) && isset($list[$key])) {
				return $list[$key];
			}
		}
		return $noValue;
	}

	/**
	 * テキスト情報を配列形式に変換して返す
	 * - 改行で分割する
	 * - 区切り文字で分割する
	 *
	 * @param string $str
	 * @return array
	 */
	public function textToArray($str)
	{
		// 文頭文末の空白を削除する
		$str = trim($str);
		// 改行コードを統一する（改行コードを変換する際はダブルクォーテーションで指定する）
		$str = preg_replace('/\r\n|\r|\n/', "\n", $str);
		// 分割（結果は配列に入る）
		// 文字によっては文字化けを起こして正しく配列に変換されない
		// preg系は、UTF8文字列を扱う場合はu修飾子が必要
		$str = preg_split('/[\s,]+/u', $str);
		// 区切り文字を利用して、キーと値を指定する場合の処理
		$keyValueArray = [];
		foreach($str as $key => $value) {
			$array = preg_split('/[:]+/', $value);
			if (count($array) > 1) {
				$keyValueArray[$array[1]] = $array[0];
			} else {
				$keyValueArray[$value] = $value;
			}
		}
		return $keyValueArray;
	}

}
