<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View.Helper
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * CSVヘルパー
 *
 * @package Baser.View.Helper
 */
class BcCsvHelper extends AppHelper {

/**
 * CSVヘッド
 *
 * @var string
 */
	public $csvHead = '';

/**
 * CSVボディ
 *
 * @var string
 */
	public $csvBody = '';

/**
 * CSVヘッドの出力
 *
 * @var boolean
 */
	public $exportCsvHead = true;

/**
 * 文字コード
 *
 * @var string
 */
	public $encoding = 'UTF-8';

/**
 * データを追加する（単数）
 *
 * @param string $modelName
 * @param array $data
 * @return void
 */
	public function addModelData($modelName, $data) {

		if (!$modelName)
			return false;
		if (!isset($data[$modelName]))
			return false;

		if (!$this->csvHead) {
			$this->csvHead = $this->_perseKey($data[$modelName]);
		}
		$this->csvBody .= $this->_perseValue($data[$modelName]);
		return true;
	}

/**
 * データをセットする（複数）
 *
 * @param string $modelName
 * @param array $datas
 * @return $csv
 */
	public function addModelDatas($modelName, $datas) {

		if (!$modelName)
			return false;
		if (!isset($datas[0][$modelName]))
			return false;

		foreach ($datas as $data) {
			$this->addModelData($modelName, $data);
		}
		return true;
	}

/**
 * モデルデータよりCSV用のheadデータを取得する
 *
 * @param array $data
 * @return string|false $head
 */
	protected function _perseKey($data) {

		if (!is_array($data))
			return false;

		$head = '';
		foreach ($data as $key => $value) {
			$head .= '"' . $key . '"' . ',';
		}
		$enc = mb_detect_encoding($head);
		$head = substr($head, 0, strlen($head) - 1) . "\n";
		if($enc != $this->encoding) {
			$head = mb_convert_encoding($head, $this->encoding, $enc);
		}

		return $head;
	}

/**
 * モデルデータよりCSV用の本体データを取得する
 *
 * @param array $data
 * @return string $body
 */
	protected function _perseValue($data) {

		if (!is_array($data))
			return false;

		$body = '';
		foreach ($data as $key => $value) {
			$value = str_replace(",", "、", $value);
			if (is_array($value)) {
				$value = implode('|', $value);
			}
			$body .= '"' . $value . '"' . ',';
		}

		$enc = mb_detect_encoding($body);
		$body = substr($body, 0, strlen($body) - 1) . "\n";
		if($enc != $this->encoding) {
			$body = mb_convert_encoding($body, $this->encoding, $enc);
		}
		return $body;
	}

/**
 * CSVファイルをダウンロードする
 *
 * @param string $fileName
 * @param boolean $debug
 * @return void|string
 */
	public function download($fileName, $debug = false) {

		if ($this->exportCsvHead) {
			$exportData = $this->csvHead . $this->csvBody;
		} else {
			$exportData = $this->csvBody;
		}

		if (!$debug) {
			Header("Content-disposition: attachment; filename=" . $fileName . ".csv");
			Header("Content-type: application/octet-stream; name=" . $fileName . ".csv");
			echo $exportData;
			exit();
		} else {
			return $exportData;
		}
	}

/**
 * ファイルを保存する
 *
 * @param $fileName
 * @return void
 */
	public function save($fileName) {

		if ($this->exportCsvHead) {
			$exportData = $this->csvHead . $this->csvBody;
		} else {
			$exportData = $this->csvBody;
		}

		$fp = fopen($fileName, "w");
		fputs($fp, $exportData, 1024 * 1000 * 10);
		fclose($fp);
	}

}
