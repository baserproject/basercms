<?php

namespace BaserCore\Service;

use BaserCore\Utility\BcSiteConfig;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;

class UtilitiesAdminService implements UtilitiesAdminServiceInterface
{

    public function getViewVarsForInfo()
    {
        return [
            'datasource' => $this->_getDriver(),
            'baserVersion' => BcSiteConfig::get('version'),
            'cakeVersion' => Configure::version()
        ];
    }

    private function _getDriver()
    {
        $drivers = ['csv' => 'CSV', 'sqlite' => 'SQLite', 'mysql' => 'MySQL', 'postgres' => 'PostgreSQL'];
        $config = TableRegistry::getTableLocator()->get('BaserCore.App')->getConnection()->config();
        $names = explode('\\', $config['driver']);
        $driver = strtolower($names[count($names) - 1]);
        if(isset($drivers[$driver])) {
            return $drivers[$driver];
        } else {
            return '';
        }
    }

}
