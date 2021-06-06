<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */

namespace BaserCore\Service\Api;

use Authentication\Authenticator\ResultInterface;
use BaserCore\Service\UsersService;
use Cake\Core\Configure;
use Firebase\JWT\JWT;

/**
 * Class UserApiService
 * @package BaserCore\Service
 */
class UserApiService extends UsersService implements UserApiServiceInterface
{
    /**
     * トークンを取得する
     * @param ResultInterface $result
     * @return array
     */
    public function getAccessToken(ResultInterface $result): array
    {
        if ($result->isValid()) {
            $algorithm = Configure::read('Jwt.algorithm');
            $privateKey = file_get_contents(Configure::read('Jwt.privateKeyPath'));
            $user = $result->getData();
            return [
                'access_token' => JWT::encode([
                        'token_type' => 'access_token',
                        'iss' => Configure::read('Jwt.iss'),
                        'sub' => $user->id,
                        'exp' => time() + Configure::read('Jwt.accessTokenExpire'),
                    ],
                    $privateKey,
                    $algorithm
                ),
                'refresh_token' => JWT::encode([
                        'token_type' => 'refresh_token',
                        'iss' => Configure::read('Jwt.iss'),
                        'sub' => $user->id,
                        'exp' => time() + Configure::read('Jwt.refreshTokenExpire'),
                    ],
                    $privateKey,
                    $algorithm
                ),
            ];
        } else {
            return [];
        }
    }

}
