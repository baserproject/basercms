<?php

namespace BaserCore\Identifier\Resolver;

use Authentication\Identifier\Resolver\ResolverInterface;
use Cake\Core\Configure;

class ConfigResolver implements ResolverInterface
{
    public function find(array $conditions, $type = self::TYPE_AND)
    {
        if (empty($conditions['token'])) {
            return false;
        } else {
            return ($conditions['token'] === Configure::read('BcApp.apiToken'))? [true] : false;
        }
    }
}
