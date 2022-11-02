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
     * キャッシュ用 Entity
     * @var SiteConfig
     */
    protected $entity;

    /**
     * SiteConfigsService constructor.
     * 
     * @checked
     * @unitTest
     * @noTodo
     */
    public function __construct()
    {
        $this->SiteConfigs = TableRegistry::getTableLocator()->get('BaserCore.SiteConfigs');
    }

    /**
     * フィールドの値を取得する
     * 
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
     * 
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function get(): SiteConfig
    {
        if (!$this->entity) {
            $this->entity = $this->SiteConfigs->newEntity(array_merge($this->SiteConfigs->getKeyValue(), [
                'mode' => Configure::read('debug'),
                'site_url' => Configure::read('BcEnv.siteUrl'),
                'ssl_url' => Configure::read('BcEnv.sslUrl'),
                'admin_ssl' => (int)Configure::read('BcApp.adminSsl'),
            ]), ['validate' => 'keyValue']);
        }
        return $this->entity;
    }

    /**
     * データを更新する
     * 
     * @param array $postData
     * @return SiteConfig|false
     * @checked
     * @noTodo
     * @unitTest
     */
    public function update(array $postData)
    {

        $siteConfig = $this->SiteConfigs->newEntity($postData, ['validate' => 'keyValue']);
        if ($siteConfig->hasErrors()) {
            return $siteConfig;
        }

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

        if ($this->isWritableEnv()) {
            if (isset($siteConfig->mode)) $this->putEnv('DEBUG', ($siteConfig->mode)? 'true' : 'false');
            if (isset($siteConfig->site_url)) $this->putEnv('SITE_URL', $siteConfig->site_url);
            if (isset($siteConfig->ssl_url)) $this->putEnv('SSL_URL', $siteConfig->ssl_url);
            if (isset($siteConfig->admin_ssl)) $this->putEnv('ADMIN_SSL', ($siteConfig->admin_ssl)? 'true' : 'false');
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
            $this->entity = null;
            return $this->get();
        }
        return false;
    }

    /**
     * .env が書き込み可能かどうか
     * 
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
     * 
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
        if (!$this->isWritableEnv()) {
            return false;
        }
        $file = new File(CONFIG . '.env');
        $contents = $file->read();
        $newLine = "export $key=\"$value\"";
        if (isset($_ENV[$key])) {
            $contents = preg_replace('/export ' . $key . '=\".*?\"/', $newLine, $contents);
        } else {
            $contents .= "\n" . $newLine;
        }
        return $file->write($contents);
    }

    /**
     * アプリケーションモードリストを取得
     * 
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getModeList(): array
    {
        return $this->SiteConfigs->getControlSource('mode');
    }

    /**
     * サイト基本設定の設定値を更新する
     *
     * @param string $name
     * @param string $value
     * @return SiteConfig
     * @checked
     * @noTodo
     * @unitTest
     */
    public function setValue($name, $value)
    {
        return $this->update([$name => $value]);
    }

    /**
     * サイト全体の設定値をリセットする
     *
     * @param string $name
     * @return SiteConfig
     * @checked
     * @noTodo
     * @unitTest
     */
    public function resetValue($name)
    {
        return $this->setValue($name, '');
    }

    /**
     * baserCMSのDBのバージョンを取得する
     * 
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getVersion():string
    {
        return (string) $this->getValue('version');
    }

    /**
     * キャッシュ用 Entity を削除
     * 
     * @checked
     * @noTodo
     * @unitTest
     */
    public function clearCache()
    {
        $this->entity = null;
    }

}
