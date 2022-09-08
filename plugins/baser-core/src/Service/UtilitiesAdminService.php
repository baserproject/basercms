<?php

namespace BaserCore\Service;

use BaserCore\Utility\BcSiteConfig;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;

class UtilitiesAdminService implements UtilitiesAdminServiceInterface
{

    public function getViewVarsForInfo()
    {
        $datasources = ['csv' => 'CSV', 'sqlite' => 'SQLite', 'mysql' => 'MySQL', 'postgres' => 'PostgreSQL'];
        $config = TableRegistry::getTableLocator()->get('BaserCore.App')->getConnection()->config();

        [$type, $name] = explode('/', $db->config['datasource'], 2);
        $datasource = preg_replace('/^bc/', '', strtolower($name));
        return [
            'datasource' => @$datasources[$datasource],
            'baserVersion' => BcSiteConfig::get('version'),
            'cakeVersion' => Configure::version()
        ];
    }

}
