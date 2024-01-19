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

namespace BaserCore\Test\Scenario;

use BaserCore\Test\Factory\LoginStoreFactory;
use CakephpFixtureFactories\Scenario\FixtureScenarioInterface;

/**
 * LoginStoresScenario
 */
class LoginStoresScenario implements FixtureScenarioInterface
{

    /**
     * load
     */
    public function load(...$args): mixed
    {
        LoginStoreFactory::make([
            'id' => 1,
            'store_key' => 'somethingkeystring',
            'user_id' => 9999,
            'prefix' => 'Admin',
            'created' => '2021-04-03 10:57:07',
            'modified' => '2021-04-04 11:20:33'
        ])->persist();
        return null;
    }

}
