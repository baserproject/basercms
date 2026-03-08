<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.2.3
 * @license       https://basercms.net/license/index.html MIT License
 */

use BaserCore\Utility\BcApiUtil;
use BaserCore\Utility\BcUpdateLog;

try {
    if (BcApiUtil::createJwt()) {
        BcUpdateLog::set(__d('baser_core', 'JWT鍵の再生成に成功しました。'));
    } else {
        BcUpdateLog::set(__d('baser_core', 'JWT鍵の再生成に失敗しました。config フォルダの書き込み権限を確認した上で、次のコマンドを実行してください。 bin/cake create jwt'));
    }
} catch (Throwable $e) {
    BcUpdateLog::set(__d('baser_core', 'JWT鍵の再生成中にエラーが発生しました。config フォルダの書き込み権限を確認した上で、次のコマンドを実行してください。 bin/cake create jwt'));
    BcUpdateLog::set($e->getMessage());
}
