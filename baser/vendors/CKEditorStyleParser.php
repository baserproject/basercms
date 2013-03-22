<?php
/**
 * CKEditorStyleParser
 * 
 * CSSを解析してCKEditorのスタイルセット用のデータ構造に変換する
 * 
 * 《記述例》
 * # 見出し
 * h2 {
 *	font-size:20px;
 *	color:#333;
 * }
 * 
 * 《変換例》
 * array(	'name'		=> '見出し',
 *			'element'	=> 'h2',
 *			'styles'	=> array(
 *				'font-size'	=>'20px',
 *				'color'		=>'#333',
 *			)
 * )
 * 
 */
class CKEditorStyleParser {

    public static function parse ($css) {

		$css = preg_replace('/\/\*.*?\*\//s', '', $css);
		
		$data = array();
        $size = strlen($css);
        for ($i = 0; $i < $size; $i++) {
            $selector = '';
            $code = '';
			$comment = '';
			for (; $i < $size && $css[$i] !== '#'; $i++);
			for ($i++; $i < $size && $css[$i] !== "\n"; $i++) {
				$comment .= $css[$i];
			}
			$i += 1;
            for (; $i < $size && $css[$i] !== '{'; $i++) {
                $selector .= $css[$i];
            }
            for ($i++; $i < $size && $css[$i] !== '}'; $i++) {
                if ($css[$i] === '\'' || $css[$i] === '"') {
                    $code .= $css[$i];
                    $i++;
                    $code .= self::readString($css[$i - 1], $css, $size, $i);
                }
                $code .= $css[$i];
            }
            $selector = trim($selector);
            $data[] = array('name' => trim($comment).'('.$selector.')', 'element' => trim($selector), 'styles' => self::parseCode($code));
        }
        return $data;
		
    }

    private static function parseCode ($code) {
        $size = strlen($code);
        $data = array();
        for ($i = 0; $i < $size; $i++) {
            $style = '';
            $content = '';
            for (; $i < $size && $code[$i] !== ':'; $i++) {
                $style .= $code[$i];
            }
            if ($i < $size && $code[$i] === ':') {
                for ($i++; $i < $size && $code[$i] !== ';'; $i++) {
                    if ($code[$i] === '\'' || $code[$i] === '"') {
                        $content .= $code[$i];
                        $i++;
                        $content .= self::readString($code[$i - 1], $code, $size, $i);
                    }
                    $content .= $code[$i];
                }
                $style = trim($style);
                $content = trim($content);
                $data[$style] = $content;
            }
        }
        return $data;
    }

    private static function readString ($target, $body, $size, &$i) {
        $data = '';
        for (; $i < $size && $body[$i - 1] !== '\\' && $body[$i] !== $target; $i++) {
            $data .= $body[$i];
        }
        return $data;
    }

}