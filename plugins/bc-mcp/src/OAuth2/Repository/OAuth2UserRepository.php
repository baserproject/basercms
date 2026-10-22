<?php
declare(strict_types=1);

namespace BcMcp\OAuth2\Repository;

use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\UserEntityInterface;
use League\OAuth2\Server\Repositories\UserRepositoryInterface;

/**
 * OAuth2 User Repository
 *
 * League OAuth2 Server の要求により実装されているが、
 * 現在のBcMcpの用途（MCP/Client Credentials Grant）では実際には使用されない
 */
class OAuth2UserRepository implements UserRepositoryInterface
{

    /**
     * ユーザー認証情報でユーザーエンティティを取得
     *
     * 注意：現在のBcMcpではClient Credentials Grantのみを使用するため、
     * このメソッドは実際には呼び出されない
     *
     * @param string $username
     * @param string $password
     * @param string $grantType
     * @param ClientEntityInterface $clientEntity
     * @return UserEntityInterface|null
     */
    public function getUserEntityByUserCredentials(
        $username,
        $password,
        $grantType,
        ClientEntityInterface $clientEntity
    ): ?UserEntityInterface
    {
        // Client Credentials Grant では使用されない
        // Authorization Code Grant を将来実装する場合は、
        // ここでbaserCMSのユーザー認証を行う
        return null;
    }

    /**
     * トークンに紐づくユーザーが有効（存在し、無効化されていない）かどうかを判定する
     *
     * 無効化・削除されたユーザーのアクセストークン・リフレッシュトークンを
     * 有効期限内でも受け付けないために使う
     *
     * @param int|string|null $userId
     * @return bool
     */
    public static function isActiveUser($userId): bool
    {
        if (!$userId || !is_numeric($userId)) return false;
        $user = \Cake\ORM\TableRegistry::getTableLocator()->get('BaserCore.Users')
            ->find()->select(['id', 'status'])->where(['Users.id' => (int)$userId])->first();
        return $user? (bool)$user->status : false;
    }

    /**
     * 指定ユーザーのアクセストークン・リフレッシュトークンをすべて失効させる
     *
     * @param int|string $userId
     * @return void
     */
    public static function revokeTokensByUserId($userId): void
    {
        if (!$userId) return;
        $locator = \Cake\ORM\TableRegistry::getTableLocator();
        $accessTokens = $locator->get('BcMcp.Oauth2AccessTokens');
        $refreshTokens = $locator->get('BcMcp.Oauth2RefreshTokens');
        $tokenIds = $accessTokens->find()
            ->select(['token_id'])
            ->where(['user_id' => (string)$userId])
            ->all()->extract('token_id')->toList();
        if (!$tokenIds) return;
        $accessTokens->updateAll(['revoked' => true], ['token_id IN' => $tokenIds]);
        $refreshTokens->updateAll(['revoked' => true], ['access_token_id IN' => $tokenIds]);
    }

}
