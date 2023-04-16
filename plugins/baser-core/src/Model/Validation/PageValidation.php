<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */

namespace BaserCore\Model\Validation;

use Cake\Core\Configure;
use Cake\Filesystem\File;
use BaserCore\Utility\BcUtil;
use Cake\Validation\Validation;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\Note;

/**
 * Class PageValidation
 */
class PageValidation extends Validation
{
    /**
     * PHP構文チェック
     *
     * @param string $check チェック対象文字列
     * @return bool
     * @checked
     * @unitTest
     * @noTodo
     */
    public static function phpValidSyntax($check)
    {
        if (empty($check)) {
            return true;
        }
        if (!Configure::read('BcApp.validSyntaxWithPage')) {
            return true;
        }
        if (!function_exists('exec')) {
            return true;
        }
        // CL版 php がインストールされてない場合はシンタックスチェックできないので true を返す
        exec('php --version 2>&1', $output, $exit);
        if ($exit !== 0) {
            return true;
        }

        if (BcUtil::isWindows()) {
            $tmpName = tempnam(TMP, "syntax");
            $tmp = new File($tmpName);
            $tmp->open("w");
            $tmp->write($check);
            $tmp->close();
            $command = sprintf("php -l %s 2>&1", escapeshellarg($tmpName));
            exec($command, $output, $exit);
            $tmp->delete();
        } else {
            $format = 'echo %s | php -l 2>&1';
            $command = sprintf($format, escapeshellarg($check));
            exec($command, $output, $exit);
        }

        if ($exit === 0) {
            return true;
        }
        $message = __d('baser_core', 'PHPの構文エラーです') . '： ' . PHP_EOL . implode(' ' . PHP_EOL, $output);
        return $message;
    }
}
