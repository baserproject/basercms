<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.6
 * @license       https://basercms.net/license/index.html MIT License
 */

namespace BaserCore\Test\TestCase\Utility;

use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcText;

/**
 * Class BcTextTest
 */
class BcTextTest extends BcTestCase
{

	/**
	 * test stripScriptTag
	 * @return void
	 * @dataProvider stripScriptTagDataProvider
	 */
	public function testStripScriptTag($content, $expect)
	{
		$result = BcText::stripScriptTag($content);
		$this->assertEquals($expect, $result, 'scriptタグを削除できません。');
	}

	public function stripScriptTagDataProvider()
	{
		return [
			[
				'content' => '<script>hoge</script>',
				'expect' => 'hoge'
			],
			[
				'content' => '<a href="http://hoge.com" class="bca-action">hoge<script>hoge</script></a>',
				'expect' => '<a href="http://hoge.com" class="bca-action">hogehoge</a>'
			]
		];
	}

}
