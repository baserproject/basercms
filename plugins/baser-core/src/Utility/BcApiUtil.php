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

namespace BaserCore\Utility;

use Cake\Core\Configure;
use Firebase\JWT\JWT;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * BcApiUtil
 */
class BcApiUtil
{

    /**
     * アクセストークンを作成する
     * @param int $userId
     * @return array
     * @checked
     * @unitTest
     * @noTodo
     */
    public static function createAccessToken(int $userId, string $prefix = 'Api/Admin')
    {
        $algorithm = Configure::read('Jwt.algorithm');
        $privateKey = file_get_contents(Configure::read('Jwt.privateKeyPath'));
        $sub = $userId;
        if($prefix) $sub = $prefix . '_' . $sub;
        return [
            'access_token' => JWT::encode([
                    'token_type' => 'access_token',
                    'iss' => Configure::read('Jwt.iss'),
                    'sub' => $sub,
                    'exp' => time() + Configure::read('Jwt.accessTokenExpire'),
                ],
                $privateKey,
                $algorithm
            ),
            'refresh_token' => JWT::encode([
                    'token_type' => 'refresh_token',
                    'iss' => Configure::read('Jwt.iss'),
                    'sub' => $sub,
                    'exp' => time() + Configure::read('Jwt.refreshTokenExpire'),
                ],
                $privateKey,
                $algorithm
            ),
        ];
    }

    /**
     * JWTキーを作成する
     *
     * @return bool
     * @noTodo
     * @checked
     * @unitTest
     */
    public static function createJwt(): bool
    {
        $command = "openssl genrsa -out " . CONFIG . "jwt.key 1024 2>&1";
        exec($command, $out, $code);
        if($code === 0) {
            $command = "openssl rsa -in " . CONFIG . "jwt.key -outform PEM -pubout -out " . CONFIG . "jwt.pem 2>&1";
            exec($command, $out, $code);
            return ($code === 0);
        } else {
            return false;
        }
    }
}
