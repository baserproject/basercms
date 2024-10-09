<?php

namespace BaserCore\Test\TestCase\vendor;

use BaserCore\TestSuite\BcTestCase;
use BaserCore\Vendor\Imageresizer;

class ImageresizerTest extends BcTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->Imageresizer = new Imageresizer();
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * Test _copyAndResize
     * @param bool $trimming
     * @param int $srcWidth
     * @param int $srcHeight
     * @param int $newWidth
     * @param int $newHeight
     * @param int $expectedWidth
     * @param int $expectedHeight
     * @dataProvider copyAndResizeDataProvider
     */
    public function testCopyAndResize($trimming, $srcWidth, $srcHeight, $newWidth, $newHeight, $expectedWidth, $expectedHeight)
    {
        $srcImage = imagecreatetruecolor($srcWidth, $srcHeight);
        $newImage = imagecreatetruecolor($newWidth, $newHeight);

        $result = $this->Imageresizer->_copyAndResize($srcImage, $newImage, $srcWidth, $srcHeight, $newWidth, $newHeight, $trimming);

        $this->assertEquals($expectedWidth, imagesx($result));
        $this->assertEquals($expectedHeight, imagesy($result));

        //delete image
        imagedestroy($srcImage);
        imagedestroy($newImage);
        imagedestroy($result);
    }

    public static function copyAndResizeDataProvider()
    {
        return [
            [
                'trimming' => true,
                'srcWidth' => 200,
                'srcHeight' => 100,
                'newWidth' => 100,
                'newHeight' => 100,
                'expectedWidth' => 100,
                'expectedHeight' => 100,
            ],
            [
                'trimming' => false,
                'srcWidth' => 200,
                'srcHeight' => 100,
                'newWidth' => 200,
                'newHeight' => 100,
                'expectedWidth' => 200,
                'expectedHeight' => 100,
            ],
        ];
    }

}
