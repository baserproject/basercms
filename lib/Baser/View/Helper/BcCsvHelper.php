<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.View.Helper
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * CSVヘルパー
 *
 * @package Baser.View.Helper
 */
class BcCsvHelper extends AppHelper
{

	/**
	 * CSVヘッド
	 *
	 * @var string
	 */
	public $csvHead = '';

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
	 * 出力データテンポラリファイルポインタ
	 * @private string
	 */
	private $_csvTmpDataFp = null;


	/**
	 * テンポラリファイルを生成する
	 */
	private function _createTmpFp()
	{
		if ($this->_csvTmpDataFp === null) {
			$this->_csvTmpDataFp = tmpfile();
		}
	}

	/**
	 * 一時ファイルのポインタを取得
	 */
	public function getCsvTmpDataFp()
	{
		$this->_createTmpFp();
		return $this->_csvTmpDataFp;
	}

	/**
	 * データを追加する（単数）
	 *
	 * @param string $modelName
	 * @param array $data
	 * @return void
	 */
	public function addModelData($modelName, $data)
	{
		if (!$modelName) {
			return false;
		}
		if (!isset($data[$modelName])) {
			return false;
		}
		$this->_createTmpFp();

		if (!$this->csvHead) {
			$this->csvHead = $this->_perseKey($data[$modelName]);
		}
		fputs($this->_csvTmpDataFp, $this->_perseValue($data[$modelName]));

		// echo memory_get_usage() / (1024 * 1024)."MB\n";
		return true;
	}

	/**
	 * データをセットする（複数）
	 *
	 * @param string $modelName
	 * @param array $datas
	 * @return $csv
	 */
	public function addModelDatas($modelName, $datas)
	{
		if (!$modelName) {
			return false;
		}
		if (!isset($datas[0][$modelName])) {
			return false;
		}

		foreach($datas as $data) {
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
	protected function _perseKey($data)
	{

		if (!is_array($data))
			return false;

		$head = '';
		foreach($data as $key => $value) {
			$head .= '"' . $key . '"' . ',';
		}
		$enc = mb_detect_encoding($head);
		$head = substr($head, 0, strlen($head) - 1) . "\n";
		if ($enc != $this->encoding) {
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
	protected function _perseValue($data)
	{

		if (!is_array($data))
			return false;

		$body = '';
		foreach($data as $key => $value) {
			$value = str_replace(",", "、", $value);
			$value = str_replace('"', '""', $value);
			if (is_array($value)) {
				$value = implode('|', $value);
			}
			$body .= '"' . $value . '"' . ',';
		}

		$enc = mb_detect_encoding($body);
		$body = substr($body, 0, strlen($body) - 1) . "\n";
		if ($enc != $this->encoding) {
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
	public function download($fileName, $debug = false)
	{
		if (!$debug) {
			for($i = 0; $i < ob_get_level(); $i++) {
				ob_end_flush();
			}
			Header("Content-disposition: attachment; filename=" . $fileName . ".csv");
			Header("Content-type: application/octet-stream; name=" . $fileName . ".csv");
			if ($this->exportCsvHead) {
				echo $this->csvHead;
			}
			if ($this->_csvTmpDataFp !== null) {
				rewind($this->_csvTmpDataFp);
				while($line = fgets($this->_csvTmpDataFp)) {
					echo $line;
				}
			}
			exit();
		} else {
			$output = '';
			if ($this->exportCsvHead) {
				$output .= $this->csvHead;
			}
			if ($this->_csvTmpDataFp !== null) {
				rewind($this->_csvTmpDataFp);
				while($line = fgets($this->_csvTmpDataFp)) {
					$output .= $line;
				}
			}
			return $output;
		}
	}

	/**
	 * ファイルを保存する
	 *
	 * @param $fileName
	 * @return void
	 */
	public function save($fileName)
	{
		$fp = fopen($fileName, "w");
		if ($this->exportCsvHead) {
			fputs($fp, $this->csvHead, 1024 * 1000 * 10);
		}
		if ($this->_csvTmpDataFp !== null) {
			rewind($this->_csvTmpDataFp);
			while($line = fgets($this->_csvTmpDataFp)) {
				fputs($fp, $line, 1024 * 1000 * 10);
			}
		}
		fclose($fp);
	}

}
