<?php
declare(strict_types=1);

namespace BcMcp\Test\Factory;

use Cake\I18n\FrozenTime;
use CakephpFixtureFactories\Factory\BaseFactory as CakephpBaseFactory;
use Faker\Generator;

/**
 * Oauth2ClientFactory
 */
class Oauth2AuthCodeFactory extends CakephpBaseFactory
{
    /**
     * Defines the Table Registry used to generate entities with
     *
     * @return string
     */
    protected function getRootTableRegistryName(): string
    {
        return 'BcMcp.Oauth2AuthCode';
    }

    /**
     * Defines the factory's default values. This is useful for
     * not nullable fields. You may use methods of the present factory here too.
     *
     * @return void
     */
    protected function setDefaultTemplate(): void
    {
        $this->setDefaultData(function(Generator $faker) {
            return [
                'code' => 'c5c91c0f3dc02fff203115be82914b9e221cf69ebe43e24e81a605ea42098909be111f29c754f2ce',
                'user_id' => 1,
                'client_id' => 'mcp-client',
                'redirect_uris' => '[]',
                'scopes' => '["mcp:read","mcp:write"]',
                'revoked' => false,
                'expires_at' => FrozenTime::now()->addMinutes(10),
                'created' => FrozenTime::now(),
                'modified' => FrozenTime::now()
            ];
        });
    }

}
