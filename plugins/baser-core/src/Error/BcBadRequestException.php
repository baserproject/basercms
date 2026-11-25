<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.4.3
 * @license       https://basercms.net/license/index.html MIT License
 */

namespace BaserCore\Error;

use Cake\Core\Exception\HttpErrorCodeInterface;

/**
 * Class BcBadRequestException
 *
 * 利用者の入力の誤りを表す例外
 *
 * サービス層やモデル層で、送信値が不正なことを呼び出し元へ伝えるために使う。
 * HTTP のステータスコードは 400 となる。
 * BcException を継承しているため、BcException を受け止めている既存の処理でもそのまま扱える。
 */
class BcBadRequestException extends BcException implements HttpErrorCodeInterface
{

    /**
     * デフォルトのステータスコード
     *
     * @var int
     */
    protected int $_defaultCode = 400;

}
