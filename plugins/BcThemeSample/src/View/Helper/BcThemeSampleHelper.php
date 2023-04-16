<?php

namespace BcThemeSample\View\Helper;

use Cake\View\Helper;

/**
 * BcSampleHelper
 *
 * テーマで利用したヘルパー（表示用関数）を記載したい場合にはここに記載します。
 * クラス名は任意です。Helperフォルダに配置したヘルパーが利用できます。
 *
 * 利用例：<?php $this->BcSample->show() ?>
 */
class BcThemeSampleHelper extends Helper {
	public function show() {
		echo 'BcThemeSample';
	}
}
