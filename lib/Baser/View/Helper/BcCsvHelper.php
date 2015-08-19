<?php
/**
 * CSVヘルパー
 * 
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2015, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2015, baserCMS Users Community
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
 * @access public
 */
	public $csvHead = '';

/**
 * CSVボディ
 *
 * @var string
 * @access public
 */
	public $csvBody = '';

/**
 * CSVヘッドの出力
 *
 * @var boolean
 * @access public
 */
	public $exportCsvHead = true;

/**
 * データを追加する（単数）
 *
 * @param string $modelName
 * @param array $data
 * @return void
 * @access public
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
 * @access protected
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
		$head = mb_convert_encoding($head, 'SJIS-WIN', $enc);

		return $head;
	}

/**
 * モデルデータよりCSV用の本体データを取得する
 *
 * @param array $data
 * @return string $body
 * @access protected
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
		$body = mb_convert_encoding($body, 'SJIS-WIN', $enc);
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
