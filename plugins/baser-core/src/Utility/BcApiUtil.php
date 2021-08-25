<?php
namespace BaserCore\Utility;

use Cake\Core\Configure;
use Firebase\JWT\JWT;

class BcApiUtil
{
    public static function createAccessToken($userId)
    {
        $algorithm = Configure::read('Jwt.algorithm');
        $privateKey = file_get_contents(Configure::read('Jwt.privateKeyPath'));
        return [
            'access_token' => JWT::encode([
                    'token_type' => 'access_token',
                    'iss' => Configure::read('Jwt.iss'),
                    'sub' => $userId,
                    'exp' => time() + Configure::read('Jwt.accessTokenExpire'),
                ],
                $privateKey,
                $algorithm
            ),
            'refresh_token' => JWT::encode([
                    'token_type' => 'refresh_token',
                    'iss' => Configure::read('Jwt.iss'),
                    'sub' => $userId,
                    'exp' => time() + Configure::read('Jwt.refreshTokenExpire'),
                ],
                $privateKey,
                $algorithm
            ),
        ];
    }
}
