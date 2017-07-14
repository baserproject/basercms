<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.Configure
 * @since			baserCMS v 3.0.7
 * @license			http://basercms.net/license/index.html
 */

/**
 * テーマの設定ファイルから設定を読み込む
 *
 * @package       Baser.Configure
 */
class BcThemeConfigReader implements ConfigReaderInterface {

/**
 * 設定ファイル名
 */
	const CONFIG_FILE_NAME = 'config.php';

/**
 * テーマディレクトリ
 *
 * @var string
 */
	protected $_path = null;

/**
 * 保存する変数
 * @var array
 */
	static public $variables = array(
		'title' => 'タイトル',
		'description' => '説明',
		'author' => '制作者',
		'url' => 'URL'
	);

/**
 * コンストラクタ
 *
 * @param string $path テーマディレクトリのパス. デフォルトは WWW_ROOT . 'theme' . DS
 */
	public function __construct($path = null) {
		if (!$path) {
			$path = WWW_ROOT . 'theme' . DS;
		}

		if (substr($path, -1) !== DS) {
			$path .= DS;
		}
		$this->_path = $path;
	}

/**
 * 指定されたテーマ名の設定ファイルを読み込む
 *
 * @param string $key テーマ名（ディレクトリ名）
 * @return array 設定の連想配列
 * @throws ConfigureException 指定されたテーマ名に対応するディレクトリや設定ファイルが存在しない時、または必要な変数が設定されていない時に例外を投げる
 */
	public function read($key) {
		$file = $this->_getFilePath($key);
		if (!is_file($file)) {
			throw new ConfigureException(__d('cake_dev', 'テーマの設定ファイルが存在しません : %s', $file));
		}

		include $file;

		$config = array();

		foreach (self::$variables as $var => $name) {
			if (!isset($$var)) {
				throw new ConfigureException(__d('baser_dev', 'テーマの %s が設定されていません : %s', array($name, $file)));
			}
			$config[$var] = $$var;
		}
		return $config;
	}

/**
 * 与えられた連想配列を設定ファイルにPHPコードとして保存する
 * 追記ではなく上書きする
 *
 * @param string $key テーマ名（ディレクトリ名）
 * @param array $data 保存する設定の連想配列
 * @return int 保存されたバイト数
 * @throws ConfigureException 指定されたテーマ名のディレクトリが存在しない時に例外を投げる
 */
	public function dump($key, $data) {
		$contents = $this->createContents($data);
		$filename = $this->_getFilePath($key);
		return file_put_contents($filename, $contents);
	}

/**
 * 与えられた連想配列からPHPコードを生成
 *
 * @param array $data 設定の連想配列
 * @return string
 */
	public function createContents(array $data) {
		$contents = '<?php' . PHP_EOL;

		foreach (self::$variables as $var => $name) {
			$value = empty($data[$var]) ? '': $data[$var];
			$contents .= '$' . $var . ' = ' . var_export($value, true) . ';' . PHP_EOL;
		}
		return $contents;
	}

/**
 * 与えられたテーマのディレクトリ名に対応する設定ファイルのパスを取得
 *
 * @param string $key テーマ名（ディレクトリ名）
 * @return string 設定ファイルのフルパス
 * @throws ConfigureException 指定されたテーマ名のディレクトリが存在しない時例外を投げる
 */
	protected function _getFilePath($key) {
		$dir = $this->_path . $key;
		if (!is_dir($dir)) {
			throw new ConfigureException(__d('baser_dev', '指定されたテーマ名のディレクトリが存在しません: %s', $dir));
		}
		return $dir . DS . self::CONFIG_FILE_NAME;
	}

}
