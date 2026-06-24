<?php

namespace BcMcp\Mcp;

use BaserCore\Service\PermissionsService;
use BaserCore\Service\PermissionsServiceInterface;
use BaserCore\Utility\BcContainerTrait;
use BaserCore\Utility\BcUtil;
use Cake\Core\Configure;

/**
 * PermissionManager
 */
class PermissionManager
{
    use BcContainerTrait;

    /**
     * 権限チェックを行う
     * @param $action
     * @param $loginGroupIds
     * @return bool
     */
    public function checkPermission($action, $loginGroupIds, $arguments): bool
    {
        $permissionUrl = $this->getPermissionUrl($action, $arguments);
        if (!$permissionUrl) return false;
        /** @var PermissionsService $permissionsService */
        $permissionsService = $this->getService(PermissionsServiceInterface::class);
        return $permissionsService->check($permissionUrl[key($permissionUrl)], $loginGroupIds, key($permissionUrl));
    }

    /**
     * 権限チェック用のURLを取得する
     * @param string $action
     * @param array $arguments
     * @return array|false
     */
    public function getPermissionUrl($action, $arguments): array|false
    {
        foreach(Configure::read('BcMcp.availableServers') as $serverClass) {

            $resourceClasses = $serverClass::getToolClasses();
            foreach($resourceClasses as $resourceClass) {
                if (!method_exists($resourceClass, 'getPermissionUrl')) {
                    throw new \RuntimeException(sprintf('Tool class %s must implement getPermissionUrls method.', $resourceClass));
                }
                $permissionUrl = $resourceClass::getPermissionUrl($action, $arguments);
                if($permissionUrl) {
                    $permissionUrl[key($permissionUrl)] = '/' . BcUtil::getBaserCorePrefix() . '/api/' . BcUtil::getAdminPrefix() . $permissionUrl[key($permissionUrl)];
                    return $permissionUrl;
                }
            }
        }
        return false;
    }

}
