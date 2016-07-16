<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Uploader.Model
 * @since			baserCMS v 3.0.10
 * @license			http://basercms.net/license/index.html
 */
/**
 * Include files
 */
/**
 * ファイルアップローダー設定モデル
 *
 * @package			baser.plugins.uploader.models
 */
class UploaderConfig extends BcPluginAppModel {
/**
 * モデル名
 * @var     string
 * @access  public
 */
	public $name = 'UploaderConfig';
/**
 * データソース
 *
 * @var		string
 * @access 	public
 */
	public $useDbConfig = 'plugin';
/**
 * プラグイン名
 *
 * @var		string
 * @access 	public
 */
	public $plugin = 'Uploader';
/**
 * バリデート
 *
 * @var		array
 * @access	public
 */
	public $validate = array(
		'large_width' => array(array(	'rule'		=> array('notEmpty'),
										'message'	=> 'PCサイズ（大）[幅] を入力してください。')),
		'large_height' => array(array(	'rule'		=> array('notEmpty'),
										'message'	=> 'PCサイズ（大）[高さ] を入力してください。')),
		'midium_width' => array(array(	'rule'		=> array('notEmpty'),
										'message'	=> 'PCサイズ（中）[幅] を入力してください。')),
		'midium_height' => array(array(	'rule'		=> array('notEmpty'),
										'message'	=> 'PCサイズ（中）[高さ] を入力してください。')),
		'small_width' => array(array(	'rule'		=> array('notEmpty'),
										'message'	=> 'PCサイズ（小）[幅] を入力してください。')),
		'small_height' => array(array(	'rule'		=> array('notEmpty'),
										'message'	=> 'PCサイズ（小）[高さ] を入力してください。')),
		'mobile_large_width' => array(array('rule'		=> array('notEmpty'),
											'message'	=> '携帯サイズ（大）[幅] を入力してください。')),
		'mobile_large_height' => array(array('rule'		=> array('notEmpty'),
											'message'	=> '携帯サイズ（大）[高さ] を入力してください。')),
		'mobile_small_width' => array(array('rule'		=> array('notEmpty'),
											'message'	=> '携帯サイズ（小）[幅] を入力してください。')),
		'mobile_small_height' => array(array('rule'		=> array('notEmpty'),
											'message'	=> '携帯サイズ（小）[幅] を入力してください。'))
	);
}