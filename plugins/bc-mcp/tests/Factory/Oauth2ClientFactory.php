<?php
declare(strict_types=1);

namespace BcMcp\Test\Factory;

use Cake\I18n\FrozenTime;
use CakephpFixtureFactories\Factory\BaseFactory as CakephpBaseFactory;
use Faker\Generator;

/**
 * Oauth2ClientFactory
 */
class Oauth2ClientFactory extends CakephpBaseFactory
{
    /**
     * Defines the Table Registry used to generate entities with
     *
     * @return string
     */
    protected function getRootTableRegistryName(): string
    {
        return 'BcMcp.Oauth2Clients';
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
                'name' => 'Generated from Admin Panel',
                'client_id' => 'mcp-client',
                'client_secret' => 'mcp-secret-key',
                'redirect_uris' => ["http://localhost"],
                'grants' => ["authorization_code", "refresh_token"],
                'scopes' => ["mcp:read", "mcp:write"],
                'is_confidential' => false,
                'created' => FrozenTime::now(),
                'modified' => FrozenTime::now()
            ];
        });
    }

}
