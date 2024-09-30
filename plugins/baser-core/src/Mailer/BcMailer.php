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

namespace BaserCore\Mailer;

use BaserCore\Event\BcEventDispatcherTrait;
use BaserCore\Service\SiteConfigsService;
use BaserCore\Service\SiteConfigsServiceInterface;
use BaserCore\Utility\BcContainerTrait;
use BaserCore\Utility\BcSiteConfig;
use Cake\Mailer\Mailer;
use Cake\Mailer\TransportFactory;
use Cake\Routing\Router;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * Class BcMailer
 */
class BcMailer extends Mailer
{

    /**
     * Trait
     */
    use BcContainerTrait;
    use BcEventDispatcherTrait;

    /**
     * プラグイン名
     *
     * @var string
     */
    protected $plugin = 'BaserCore';

    /**
     * Constructor
     *
     * @param null $config
     * @checked
     * @noTodo
     */
    public function __construct($config = null)
    {
        parent::__construct($config);
        $request = Router::getRequest();
        $site = $request ? $request->getAttribute('currentSite') : null;
        $this->setEmailTransport();
        if ($site) $this->viewBuilder()
            ->setTheme($site->theme)
            ->setClassName('BaserCore.BcFrontEmail');
        $this->setFrom([
            BcSiteConfig::get('email') => BcSiteConfig::get('formal_name')
        ]);
    }

    /**
     * Emailのトランスポート設定を行う
     * @return void
     * @checked
     * @noTodo
     */
    public function setEmailTransport()
    {
        /** @var SiteConfigsService $siteConfigsService */
        $siteConfigsService = $this->getService(SiteConfigsServiceInterface::class);
        $siteConfig = $siteConfigsService->get();
        if ($siteConfig->smtp_host && $siteConfig->smtp_user && $siteConfig->smtp_password) {
            $type = 'smtp';
            $config = [
                'className' => 'Smtp',
                'host' => $siteConfig->smtp_host,
                'username' => $siteConfig->smtp_user,
                'password' => $siteConfig->smtp_password
            ];
            if ($siteConfig->smtp_port) $config['port'] = $siteConfig->smtp_port;
            if ($siteConfig->smtp_tls) $config['tls'] = $siteConfig->smtp_tls;
            if (!TransportFactory::getConfig($type)) {
                TransportFactory::setConfig($type, $config);
            }
        } else {
            $type = 'default';
            if ($siteConfig->mail_additional_parameters) {
                $config = TransportFactory::getConfig($type);
                TransportFactory::drop($type);
                $config['additionalParameters'] = $siteConfig->mail_additional_parameters;
                TransportFactory::setConfig($type, $config);
            }
        }
        $this->setTransport($type);
    }

    /**
     * プラグイン名取得
     *
     * @return string
     * @checked
     * @noTodo
     * @UnitTest
     */
    public function getPlugin(): ?string
    {
        return $this->plugin;
    }

    /**
     * Render content and send email using configured transport.
     *
     * @param string $content Content.
     * @return array
     * @psalm-return array{headers: string, message: string}
     * @checked
     * @noTodo
     */
    public function deliver(string $content = ''): array
    {
        $this->dispatchLayerEvent('beforeDeliver');
        return parent::deliver($content);
    }

}
