<?php

/**
 * 画像のリサイズを行う
 *
 * @filesource
 * @copyright        Copyright (c) 2007, Catchup
 * @link                https://basercms.net
 * @license            MIT License
 */
class Imageresizer
{
	/**
	 * 画像をリサイズする
	 * @param string    ソースファイルのパス
	 * @param string    保存先のパス
	 * @param int    幅
	 * @param int        高さ
	 * @param array    圧縮レベル (JPEG: 100 (0-100), PNG: 6 (0-9))
	 * @return    boolean
	 */
	function resize($imgPath, $savePath = null, $newWidth = null, $newHeight = null, $trimming = false, $quality = [])
	{

		// 画像種類別の圧縮レベルのデフォルト値
		$quality = $quality + [
				IMAGETYPE_JPEG => 100,    //  0 - 100
				IMAGETYPE_PNG => 6,        // 	0 - 9
			];

		// 元画像のサイズを取得
		$imginfo = getimagesize($imgPath);
		$srcWidth = $imginfo[0];
		$srcHeight = $imginfo[1];
		$image_type = $imginfo[2];

		/* 処理できる画像は、縦×横のピクセル数が1,900,000程度迄の場合があった。*/
		/* アイデュのサーバーでは問題なかった */
		/*if($srcWidth > 1600 || $srcHeight > 1200){
			unlink($imgPath);
			return false;
		}*/

		// 元となる画像のオブジェクトを生成
		switch($image_type) {
			case IMAGETYPE_GIF:
				$srcImage = imagecreatefromgif($imgPath);
				break;

			case IMAGETYPE_JPEG:
				$srcImage = imagecreatefromjpeg($imgPath);
				break;

			case IMAGETYPE_PNG:
				$srcImage = imagecreatefrompng($imgPath);
				break;

			default:
				return false;
		}

		if (!$srcImage) {
			return false;
		}

		// 新しい画像のサイズを取得
		if (!$newWidth) {

			if ($srcHeight < $newHeight) {
				$rate = 1;
			} else {
				$rate = $srcHeight / $newHeight;
			}

		} elseif (!$newHeight) {

			if ($srcWidth < $newWidth) {
				$rate = 1;
			} else {
				$rate = $srcWidth / $newWidth;
			}

		} else {

			$w = $srcWidth / $newWidth;
			$h = $srcHeight / $newHeight;
			if ($w < 1 && $h < 1) {
				$rate = 1;
			} elseif ($w > $h) {
				$rate = $w;
			} else {
				$rate = $h;
			}

		}

		if (!$trimming) {
			$newWidth = $srcWidth / $rate;
			$newHeight = $srcHeight / $rate;
		}

		// 新しい画像のベースを作成
		switch($image_type) {
			case IMAGETYPE_GIF:
				$newImage = imagecreatetruecolor($newWidth, $newHeight);
				$alpha = imagecolorallocatealpha($newImage, 255, 255, 255, 0);
				imagefill($newImage, 0, 0, $alpha);
				imagecolortransparent($newImage, $alpha);
				break;
			case IMAGETYPE_PNG:
				$newImage = imagecreatetruecolor($newWidth, $newHeight);
				imagealphablending($newImage, false);
				imagesavealpha($newImage, true);
				break;
			default:
				$newImage = imagecreatetruecolor($newWidth, $newHeight);
				$color = imagecolorallocatealpha($newImage, 255, 255, 255, 0);
				imagealphablending($newImage, true);
				imagesavealpha($newImage, true);
				imagefill($newImage, 0, 0, $color);
				break;
		}

		// 画像をコピーし、リサイズする
		$newImage = $this->_copyAndResize($srcImage, $newImage, $srcWidth, $srcHeight, $newWidth, $newHeight, $trimming);
		imagedestroy($srcImage);

		if ($savePath && file_exists($savePath)) {
			@unlink($savePath);
		}

		switch($image_type) {
			case IMAGETYPE_GIF:
				if ($savePath) {
					imagegif($newImage, $savePath);
				} else {
					imagegif($newImage);
				}
				break;

			case IMAGETYPE_JPEG:
				imagejpeg($newImage, $savePath, $quality[IMAGETYPE_JPEG]);
				break;

			case IMAGETYPE_PNG:
				if ($savePath) {
					imagepng($newImage, $savePath, $quality[IMAGETYPE_PNG]);
				} else {
					imagepng($newImage);
				}
				break;

			default:
				return false;
		}

		imagedestroy($newImage);

		if ($savePath) {
			chmod($savePath, 0666);
		}

		return true;

	}

	/**
	 * ファイルをコピーし、リサイズする
	 * @param Image    ソースイメージオブジェクト
	 * @param Image    リサイズイメージ格納用のイメージオブジェクト
	 * @param int    元の幅
	 * @param int        元の高さ
	 * @param int    新しい幅
	 * @param int        新しい高さ
	 * @return    Image    新しいイメージオブジェクト
	 */
	function _copyAndResize($srcImage, $newImage, $srcWidth, $srcHeight, $newWidth, $newHeight, $trimming = false)
	{

		if ($trimming) {
			if ($srcWidth > $srcHeight) {
				$x = ($srcWidth - $srcHeight) / 2;
				$y = 0;
				$srcWidth = $srcHeight;
			} elseif ($srcWidth < $srcHeight) {
				$x = 0;
				$y = ($srcHeight - $srcWidth) / 2;
				$srcHeight = $srcWidth;
			} else {
				$x = 0;
				$y = 0;
			}
		} else {
			$x = 0;
			$y = 0;
		}
		imagecopyresampled($newImage, $srcImage, 0, 0, $x, $y, $newWidth, $newHeight, $srcWidth, $srcHeight);
		return $newImage;

	}

	/**
	 * ベース画像を作成する
	 * @param int    幅
	 * @param int        高さ
	 * @return    Image    イメージオブジェクト
	 */
	function _createBaseImange($width, $height)
	{

		//新しい画像を生成
		$image = imagecreatetruecolor($width, $height);
		$color = imagecolorallocatealpha($image, 255, 255, 255, 0);
		imagealphablending($image, true);
		imagesavealpha($image, true);
		imagefill($image, 0, 0, $color);
		return $image;

	}

}
