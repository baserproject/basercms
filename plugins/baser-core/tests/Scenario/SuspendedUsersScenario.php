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

use BaserCore\Test\Factory\UserFactory;
use CakephpFixtureFactories\Scenario\FixtureScenarioInterface;

/**
 * SuspendedUsersScenario
 * 無効ユーザーを追加するシナリオ
 *
 * 利用する場合は、テーブルの初期化に次のフィクスチャの定義が必要
 * - plugin.BaserCore.Factory/Users
 */
class SuspendedUsersScenario implements FixtureScenarioInterface
{

    /**
     * load
     * @param int $n
     * @param ...$args
     * @return \BaserCore\Model\Entity\User|\BaserCore\Model\Entity\User[]|mixed|void
     */
    public function load($n = 1, ...$args)
    {
        return UserFactory::make($n)->suspended()->persist();
    }

}
