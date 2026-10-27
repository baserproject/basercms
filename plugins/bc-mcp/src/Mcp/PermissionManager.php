<?php

namespace BcMcp\Mcp;

use BaserCore\Service\PermissionsService;
use BaserCore\Service\PermissionsServiceInterface;
use BaserCore\Utility\BcContainerTrait;
use BaserCore\Utility\BcUtil;
use Cake\Core\Configure;
use Cake\Http\ServerRequest;
use Cake\Routing\Router;

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
        // PermissionsService は解決できない URL やプレフィックスのない URL を許可とみなすため、
        // 管理 API として解決できない URL はここで拒否する
        if (!$this->isApiAdminUrl($permissionUrl[key($permissionUrl)])) return false;
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
                    // 管理 API のパスは管理画面のプレフィックス（ADMIN_PREFIX）に依存しないため、
                    // Api/Admin の alias から組み立てる
                    $permissionUrl[key($permissionUrl)] = '/' . BcUtil::getBaserCorePrefix()
                        . Configure::read('BcPrefixAuth.Api/Admin.alias') . $permissionUrl[key($permissionUrl)];
                    return $permissionUrl;
                }
            }
        }
        return false;
    }

    /**
     * 管理 API（Api/Admin）として解決できる URL か判定する
     * @param string $url
     * @return bool
     */
    private function isApiAdminUrl(string $url): bool
    {
        try {
            $params = Router::parseRequest(new ServerRequest(['url' => $url]));
        } catch (\Throwable) {
            return false;
        }
        return ($params['prefix'] ?? null) === 'Api/Admin';
    }

}
