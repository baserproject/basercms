<?php

/**
 * ThemeConfigモデルのテスト
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2015, baserCMS ThemeConfigs Community <http://sites.google.com/site/baserThemeConfigs/>
 *
 * @copyright   Copyright 2008 - 2015, baserCMS ThemeConfigs Community
 * @link      http://basercms.net baserCMS Project
 * @since     baserCMS v 3.0.0-beta
 * @license     http://basercms.net/license/index.html
 */
App::uses('ThemeConfig', 'Model');

/**
 * ThemeConfigTest class
 * 
 * class NonAssosiationThemeConfig extends ThemeConfig {
 *  public $name = 'ThemeConfig';
 *  public $belongsTo = array();
 *  public $hasMany = array();
 * }
 * 
 * @package Baser.Test.Case.Model
 */
class ThemeConfigTest extends BaserTestCase {

  public $fixtures = array(
    'baser.Default.Page',
    'baser.Default.ThemeConfig',
  );

  public function setUp() {
    parent::setUp();
    $this->ThemeConfig = ClassRegistry::init('ThemeConfig');
  }

  public function tearDown() {
    unset($this->ThemeConfig);
    parent::tearDown();
  }

/**
 * 画像を保存する
 * 
 * MEMO : move_uploaded_file()が成功しないため、スキップ
 * 
 * @param array $data
 */
  public function testSaveImage() {

    $this->markTestIncomplete('このテストは、まだ実装されていません。');

    // ダミーの画像を作成
    $sourcePath = WWW_ROOT . 'theme' . DS . 'm-single' . DS . 'img' . DS . 'logo.png';
    $dummyPath = WWW_ROOT . 'files' . DS . 'theme_configs' . DS . 'logo.png';
    $dummyTmpPath = WWW_ROOT . 'files' . DS . 'theme_configs' . DS . 'logo_tmp.png';
    copy($sourcePath, $dummyPath);
    copy($sourcePath, $dummyTmpPath);

    $data = array('ThemeConfig' => array(
      'logo' => array(
        'name' => 'logo.png',
        'tmp_name' => WWW_ROOT . 'files' . DS . 'theme_configs' . DS .'logo_tmp.png',
        )
      )
    );
    $this->ThemeConfig->saveImage($data);

    // // ダミーの画像を削除
    @unlink($dummyPath);
    @unlink($dummyTmpPath);
  }

/**
 * 画像を削除する
 * 
 * @param array $data
 * @param array $expected true 画像が存在する / false 画像が存在しない(削除されている)
 * @param string $message テストが失敗した時に表示されるメッセージ
 * @dataProvider deleteImageDataProvider
 */
  public function testDeleteImage($data, $expected, $message = null) {
    
    // ダミーの画像を作成
    $sourcePath = WWW_ROOT . 'theme' . DS . 'm-single' . DS . 'img' . DS . 'logo.png';
    $dummyPath = WWW_ROOT . 'files' . DS . 'theme_configs' . DS . 'logo.png';
    copy($sourcePath, $dummyPath);

    $data = array('ThemeConfig' => $data);
    $this->ThemeConfig->deleteImage($data);
    
    // 画像の有無
    if ($expected) {
      $this->assertFileExists($dummyPath, $message);
    } else {
      $this->assertFileNotExists($dummyPath, $message);
    }

    // ダミーの画像を削除
    @unlink($dummyPath);

  }

/**
 * deleteImage用データプロバイダ
 *
 * @return array
 */
  public function deleteImageDataProvider() {
    return array(
      array(array('logo_delete' => true), false, '画像を削除できません'),
      array(array('logo' => true), true, '画像が削除されています'),
      array(array(), true, '画像が削除されています'),
    );
  }

/**
 * テーマカラー設定を保存する
 * 
 * @param array $data 設定するテーマカラーのデータ
 * @param array $expected 期待値
 * @param string $message テストが失敗した時に表示されるメッセージ
 * @dataProvider updateColorConfigDataProvider
 */
  public function testUpdateColorConfig($data, $expected, $message = null) {

    // 元のファイルを取得($dataが設定されてない場合、元のファイルが削除されるため)
    $configCssPathOriginal = getViewPath() . 'css' . DS . 'config.css';
    $FileOriginal = new File($configCssPathOriginal);
    $config = $FileOriginal->read();

    // テーマーカラーの設定を実行
    $data = array('ThemeConfig' => $data );
    $this->ThemeConfig->updateColorConfig($data);

    // 元のファイルを再生成
    $FileOriginal->write($config);
    $FileOriginal->close();    

    // 生成したconfig.cssをの内容を取得
    $configCssPath = WWW_ROOT . 'files' . DS . 'theme_configs' . DS . 'config.css';
    $File = new File($configCssPath);
    $setting = $File->read();
    $File->close();
    unlink($configCssPath);

    $this->assertRegExp('/' . $expected . '/s', $setting, $message);

  }


/**
 * updateColorConfig用データプロバイダ
 *
 * @return array
 */
  public function updateColorConfigDataProvider() {
    return array(
      array(array( 'color_main' => '000000' ), '#000000', 'テーマカラーを設定できません'),
      array(array( 'color_main' => '000000', 'color_sub' => '111111' ), '#000000.*#111111', 'テーマカラーを複数設定できません'),
      array(array('hoge' => '000000'), '{.}', '$dataが適切でないのにcssの要素が空ではありません'),
      array(array(), '{.}', '$dataがないのにcssの要素が空ではありません'),
    );
  }
}
