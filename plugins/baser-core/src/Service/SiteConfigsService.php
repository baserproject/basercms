<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */

namespace BaserCore\Service;

use BaserCore\Model\Entity\SiteConfig;
use BaserCore\Model\Table\SiteConfigsTable;
use BaserCore\Utility\BcContainerTrait;
use Cake\Core\Configure;
use Cake\Filesystem\File;
use Cake\ORM\TableRegistry;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * Class SiteConfigsMockService
 * @package BaserCore\Service
 */
class SiteConfigsService implements SiteConfigsServiceInterface
{

    /**
     * Trait
     */
    use BcContainerTrait;

    /**
     * SiteConfigs Table
     * @var SiteConfigsTable
     */
    public $SiteConfigs;

    /**
     * SiteConfigsService constructor.
     */
    public function __construct()
    {
        $this->SiteConfigs = TableRegistry::getTableLocator()->get('BaserCore.SiteConfigs');
    }

    /**
     * フィールドの値を取得する
     * @param string $fieldName
     * @return string|null
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getValue($fieldName): ?string
    {
        $siteConfig = $this->get();
        return $siteConfig->{$fieldName};
    }

    /**
     * データを取得する
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function get(): SiteConfig
    {
        return $this->SiteConfigs->newEntity(array_merge($this->SiteConfigs->getKeyValue(), [
            'mode' => Configure::read('debug'),
            'site_url' => Configure::read('BcEnv.siteUrl'),
            'ssl_url' => Configure::read('BcEnv.sslUrl'),
            'admin_ssl' => (int)Configure::read('BcApp.adminSsl'),
        ]));
    }

    /**
     * データを更新する
     * @param array $postData
     * @return SiteConfig
     * @checked
     * @noTodo
     * @unitTest
     */
    public function update(array $postData): SiteConfig
    {

        $siteConfig = $this->SiteConfigs->newEntity($postData, ['validate' => 'keyValue']);
        if($siteConfig->hasErrors()) {
            return $siteConfig;
        }

        // TODO 未実装のためコメントアウト
        /* >>>
        $beforeSiteConfig = $this->get();
        if ($beforeSiteConfig->admin_theme !== $siteConfig->admin_theme) {
            $this->BcManager->deleteAdminAssets();
            $this->BcManager->deployAdminAssets();
        }
        <<< */

        $sites = TableRegistry::getTableLocator()->get('BaserCore.Sites');
        if ($siteConfig->use_site_device_setting === "0" && $this->SiteConfigs->isChange('use_site_device_setting', "0")) {
            $sites->resetDevice();
        }
        if ($siteConfig->use_site_lang_setting === "0" && $this->SiteConfigs->isChange('use_site_lang_setting', "0")) {
            $sites->resetLang();
        }

        if ($siteConfig->site_url && !preg_match('/\/$/', $siteConfig->site_url)) {
            $siteConfig->site_url .= '/';
        }
        if ($siteConfig->ssl_url && !preg_match('/\/$/', $siteConfig->ssl_url)) {
            $siteConfig->ssl_url .= '/';
        }

        if($this->isWritableEnv()) {
            if(isset($siteConfig->mode)) $this->putEnv('DEBUG', ($siteConfig->mode)? 'true' : 'false');
            if(isset($siteConfig->site_url)) $this->putEnv('SITE_URL', $siteConfig->site_url);
            if(isset($siteConfig->ssl_url)) $this->putEnv('SSL_URL', $siteConfig->ssl_url);
            if(isset($siteConfig->admin_ssl)) $this->putEnv('ADMIN_SSL', ($siteConfig->admin_ssl)? 'true' : 'false');
        }

        $siteConfigArray = $siteConfig->toArray();
        unset($siteConfigArray['mode'],
            $siteConfigArray['site_url'],
            $siteConfigArray['ssl_url'],
            $siteConfigArray['admin_ssl'],
            $siteConfigArray['dummy-site_url'],
            $siteConfigArray['dummy-ssl_url']
        );

        if ($this->SiteConfigs->saveKeyValue($siteConfigArray)) {
            return $this->get();
        }
        return $siteConfig;
    }

    /**
     * .env が書き込み可能かどうか
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function isWritableEnv(): bool
    {
        return is_writable(CONFIG . '.env');
    }

    /**
     * .env に設定値を書き込む
     * @param $key
     * @param $value
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function putEnv($key, $value): bool
    {
        $key = str_replace([';', '"'], '', $key);
        $value = str_replace([';', '"'], '', $value);
        if(!$this->isWritableEnv()) {
            return false;
        }
        $file = new File(CONFIG . '.env');
        $contents = $file->read();
        $newLine = "export $key=\"$value\"";
        if(isset($_ENV[$key])) {
            $contents = preg_replace('/export ' . $key . '=\".*?\"/', $newLine, $contents);
        } else {
            $contents .= "\n" . $newLine;
        }
        return $file->write($contents);
    }

    /**
     * アプリケーションモードリストを取得
     * @return array
     */
    public function getModeList(): array
    {
        return $this->SiteConfigs->getControlSource('mode');
    }

}
