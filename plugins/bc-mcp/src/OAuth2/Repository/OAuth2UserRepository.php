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

}
