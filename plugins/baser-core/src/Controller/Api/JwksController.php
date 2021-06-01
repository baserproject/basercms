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

namespace BaserCore\Controller\Api;

use Cake\Core\Configure;
use Firebase\JWT\JWT;

/**
 * Class JwksController
 * @package BaserCore\Controller\Api
 */
class JwksController extends BcApiController
{

    /**
     * Initialize
     */
    public function initialize(): void
    {
        parent::initialize();
        $this->Authentication->allowUnauthenticated(['index']);
    }

    /**
     * トークンを検証する（RS256のみ）
     *
     * # PHPでの検証コード例
     * JWT::decode($jwt, JWK::parseKeySet($keys), [Configure::read('Jwt.algorithm')])
     */
    public function index()
    {
        $pubKey = file_get_contents(Configure::read('Jwt.publicKeyPath'));
        $res = openssl_pkey_get_public($pubKey);
        $detail = openssl_pkey_get_details($res);
        $key = [
            'kid' => Configure::read('Jwt.kid'),
            'kty' => 'RSA',
            'alg' => Configure::read('Jwt.algorithm'),
            'use' => 'sig',
            'e' => JWT::urlsafeB64Encode($detail['rsa']['e']),
            'n' => JWT::urlsafeB64Encode($detail['rsa']['n']),
        ];
        $keys['keys'][] = $key;
        $this->viewBuilder()->setClassName('Json');
        $this->set(compact('keys'));
        $this->viewBuilder()->setOption('serialize', 'keys');
    }
}
