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

namespace BaserCore\Test\Scenario\Middleware;

use BaserCore\Test\Factory\ContentFactory;
use BaserCore\Test\Scenario\InitAppScenario;
use CakephpFixtureFactories\Scenario\FixtureScenarioInterface;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * InitAppScenario
 */
class BcUpdateFilterMiddlewareScenario implements FixtureScenarioInterface
{

    /**
     * Trait
     */
    use ScenarioAwareTrait;

    /**
     * load
     */
    public function load(...$args)
    {
        $this->loadFixtureScenario(InitAppScenario::class);
        ContentFactory::make()->top()->persist();
    }

}
