<?php
// TODO ucmitz 未実装
return;

//namespace BcBlog\Test\TestCase\Model\Validation;

use BaserCore\TestSuite\BcTestCase;

class BlogContentValidationTest extends BcTestCase
{
    /**
     * アイキャッチ画像サイズバリデーション
     *
     * @dataProvider checkEyeCatchSizeDataProvider
     */
    public function testCheckEyeCatchSize($thumb_width, $thumb_height, $mobile_thumb_width, $mobile_thumb_height, $expected)
    {
        // TODO ucmitz 未調整
        $this->markTestIncomplete('未実装のためスキップ');
        $this->BlogContent->data['BlogContent']['eye_catch_size'] = BcUtil::serialize([
            'thumb_width' => $thumb_width,
            'thumb_height' => $thumb_height,
            'mobile_thumb_width' => $mobile_thumb_width,
            'mobile_thumb_height' => $mobile_thumb_height
        ]);
        $this->assertEquals($this->BlogContent->checkEyeCatchSize(), $expected);
    }

    public function checkEyeCatchSizeDataProvider()
    {
        return [
            [600, 600, 100, 100, true],
            ['', 600, 100, 100, false],
            [600, '', 100, 100, false],
            [600, 600, '', 100, false],
            [600, 600, 100, '', false],
        ];
    }

}
