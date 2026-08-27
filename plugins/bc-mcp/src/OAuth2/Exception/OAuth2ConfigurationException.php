<?php
declare(strict_types=1);

namespace BcMcp\OAuth2\Exception;

use RuntimeException;

/**
 * OAuth2 の設定不備を表す例外
 *
 * 暗号化キーのように、欠けたまま動かすと安全性が損なわれる設定が
 * 未設定である事を表す。各コントローラはこれを捕捉して 503 を返す。
 */
class OAuth2ConfigurationException extends RuntimeException
{
}
