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

namespace BaserCore\Service;

use Authentication\Authenticator\ResultInterface;
use Cake\Core\Configure;
use Firebase\JWT\JWT;

/**
 * Class UserApiService
 * @package BaserCore\Service
 */
class UserApiService extends UsersService implements UserApiServiceInterface
{
    /**
     * ログイントークンを取得する
     * @param ResultInterface $result
     * @return array
     */
    public function getLoginToken(ResultInterface $result): array
    {
        if ($result->isValid()) {
            $user = $result->getData();
            return [
                'token' => JWT::encode([
                    'iss' => Configure::read('Jwt.iss'),
                    'sub' => $user->id,
                    'exp' => time() + Configure::read('Jwt.expire'),
                ],
                    file_get_contents(Configure::read('Jwt.privateKeyPath')),
                    Configure::read('Jwt.algorithm')
                ),
            ];
        } else {
            return [];
        }
    }

}
