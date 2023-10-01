<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.5
 * @license       https://basercms.net/license/index.html MIT License
 */

namespace BaserCore\Test\TestCase\Utility;

use BaserCore\Utility\BcFile;
use BaserCore\Utility\BcFolder;
use PHPUnit\Framework\TestCase;

/**
 * Class BcFileTest
 */
class BcFileTest extends TestCase
{

    /**
     * test __construct
     * @return void
     */
	public function test__construct()
	{
        $path = TMP_TESTS . 'test';
        $folder = new BcFile($path);
        $this->assertEquals($path, $folder->getPath());
	}

    /**
     * test Create
     * @return void
     */
	public function testCreate()
	{
        $path = TMP_TESTS . 'test' . DS . 'test.txt';
        $this->assertTrue((new BcFile($path))->create());
        $this->assertTrue(is_file($path));
        (new BcFolder(dirname($path)))->delete();
	}
}
