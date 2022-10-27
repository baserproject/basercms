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

namespace BaserCore\Middleware;

use BaserCore\Error\BcException;
use BaserCore\Model\Entity\SiteConfig;
use BaserCore\Utility\BcContainerTrait;
use BaserCore\Utility\BcUtil;
use Cake\Core\Configure;
use Cake\Http\Exception\NotFoundException;
use Cake\Log\LogTrait;
use Cake\ORM\TableRegistry;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * BcUpdateFilterMiddleware
 */
class BcUpdateFilterMiddleware implements MiddlewareInterface
{

    /**
     * Trait
     */
    use LogTrait;
    use BcContainerTrait;

    /**
     * Process
     * データベースの BaserCore のバージョンより、ソースコードのバージョンが高い場合、
     * BaserCore のアップデーターのURL以外のURLにアクセスした場合、メンテンナンス画面にリダイレクトする。
     * アップデーターのURLにアクセスした場合は、定数 BC_ALLOWED_UPDATE に有効である事をマーキングする
     * BC_ALLOWED_UPDATE は、PluginsController::beforeFilter() で参照している
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     * @checked
     * @noTodo
     * @unitTest
     */
    public function process(
        ServerRequestInterface  $request,
        RequestHandlerInterface $handler
    ): ResponseInterface
    {
        $allowedUpdate = false;
        if (BcUtil::isInstalled()) {
            $siteConfigs = TableRegistry::getTableLocator()->get('BaserCore.SiteConfigs');
            /* @var SiteConfig $siteConfig */
            $siteConfig = $siteConfigs->find()->where(['name' => 'version'])->first();
            if($siteConfig) {
                $dbVersion = $siteConfig->value;
            } else {
                $dbVersion = '0';
            }
            $sourceVersion = BcUtil::getVersion();
            if ($request->getAttribute('here') === '/' . Configure::read('BcApp.updateKey')) {
                if($dbVersion === $sourceVersion) throw new NotFoundException();
                $allowedUpdate = true;
            } elseif (!$request->is('maintenance') && BcUtil::verpoint($sourceVersion) > BcUtil::verpoint($dbVersion)) {
                if (!BcUtil::isConsole()) {
                    $this->log('プログラムとデータベースのバージョンが異なります。');
                    header('Location: ' . BcUtil::topLevelUrl(false) . BcUtil::baseUrl() . 'maintenance/index');
                } else {
                    throw new BcException(__d('baser', 'プログラムとデータベースのバージョンが異なるため、強制終了します。データベースのバージョンを調整して、再実行してください。'));
                }
            }
        }
        Configure::write('BcRequest.isUpdater', $allowedUpdate);
        return $handler->handle($request);
    }
}
