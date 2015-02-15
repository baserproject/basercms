<?php

class BcConfigString {

/**
 * 中身の文字列
 * @var string
 */
	public $content;

/**
 * コンストラクタ
 *
 * @param string $content 文字列
 */
	public function __construct($content = '') {
		$this->content = $content;
	}

/**
 * 一度に複数の変数をupsert
 *
 * @param array $data 変数名と値のハッシュ
 * @return bool
 */
	public function upsertMany($data) {
		$failed = false;
		foreach ($data as $key => $value) {
			if (!$this->upsert($key, $value)) {
				$failed = true;
			}
		}
		return !$failed;
	}

/**
 * 変数が存在すれば更新し、なければ追加する
 *
 * @param string $key 変数名
 * @param string $value 値
 * @return bool
 */
	public function upsert($key, $value) {
		if ($this->has($key)) {
			return $this->update($key, $value);
		}
		return $this->insert($key, $value);
	}

/**
 * 変数を更新
 *
 * @param string $key 変数名
 * @param string $value 値
 * @return bool
 */
	public function update($key, $value) {
		$code = $this->_getSanitizedCode($key, $value);
		$regex = $this->_getRegex($key);
		$result = preg_replace($regex, $code, $this->content);
		if (is_null($result)) {
			return false;
		}
		$this->content = $result;
		return true;
	}

/**
 * 変数を新規に挿入
 *
 * @param string $key 変数名
 * @param string $value 値
 * @return bool
 */
	public function insert($key, $value) {
		$code = $this->_getSanitizedCode($key, $value);
		$regex = '/\?>[\s\n]*?\z/';

		//PHPコードの末尾にPHPの閉じタグがあるかどうかで分岐
		if (preg_match($regex, $this->content)) {
			$result = preg_replace($regex, $code . PHP_EOL . '?>' . PHP_EOL, $this->content);
		} else {
			$result = $this->content . PHP_EOL . $code . PHP_EOL . '?>' . PHP_EOL;
		}

		if (is_null($result)) {
			return false;
		}

		$this->content = $result;
		return true;
	}

/**
 * 変数を含むかどうかを判定
 *
 * @param string $key 変数名
 * @return bool
 */
	public function has($key) {
		return (bool)preg_match($this->_getRegex($key), $this->content);
	}

/**
 * PHP文字列を無害化する
 *
 * @param string $string 文字列
 * @return bool|string
 */
	public function sanitizePhpString($string) {
		$encoding = mb_detect_encoding($string);
		if ($encoding === false) {
			return false;
		}
		if ($encoding !== 'UTF-8') {
			$string = mb_convert_encoding($string, 'UTF-8', $encoding);
		}
		return addslashes($string);
	}

/**
 * 既存の変数検索用の正規表現を取得
 *
 * @param string $key 変数名
 * @return string
 */
	protected function _getRegex($key) {
		return sprintf('/\$%s[\s\n]*?=[\s\n]*?\'.*?\';/s', $key);
	}

/**
 * 無害化された変数代入のコードを取得
 *
 * @param string $key 変数名
 * @param string $value 値
 * @return string
 */
	protected function _getSanitizedCode($key, $value) {
		return sprintf('$%s = \'%s\';', $key, $this->sanitizePhpString($value));
	}

}