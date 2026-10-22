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

use Cake\Log\Log;
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
     * @unitTest
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
        $adminMail = BcSiteConfig::get('email');
        if (strpos($adminMail, ',') !== false) {
            [$fromAdmin] = explode(',', $adminMail);
        } else {
            $fromAdmin = $adminMail;
        }
        $this->setFrom($fromAdmin, BcSiteConfig::get('formal_name'));
    }

    /**
     * Emailのトランスポート設定を行う
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function setEmailTransport()
    {
        /** @var SiteConfigsService $siteConfigsService */
        $siteConfigsService = $this->getService(SiteConfigsServiceInterface::class);
        $siteConfig = $siteConfigsService->get();
        if ($siteConfig->smtp_host) {
            $type = 'smtp';
            $config = [
                'className' => 'Smtp',
                'host' => $siteConfig->smtp_host,
                'username' => $siteConfig->smtp_user ? $siteConfig->smtp_user : null,
                'password' => $siteConfig->smtp_password ? $siteConfig->smtp_password : null,
            ];
            $config['port'] = $siteConfig->smtp_port ? $siteConfig->smtp_port : 25;
            if ($siteConfig->smtp_tls) $config['tls'] = $siteConfig->smtp_tls;
            if (!TransportFactory::getConfig($type)) {
                TransportFactory::setConfig($type, $config);
            }
        } else {
            $type = 'default';
            if ($siteConfig->mail_additional_parameters) {
                // mail() の追加パラメータは sendmail のコマンドラインへ渡されるため、
                // エンベロープ送信者の指定（-f メールアドレス）以外は受け付けない。
                // 任意の値を許すと -X / -C 等のオプションで任意パスへの書き込みに悪用される
                if (self::isValidAdditionalParameters($siteConfig->mail_additional_parameters)) {
                    $config = TransportFactory::getConfig($type);
                    TransportFactory::drop($type);
                    $config['additionalParameters'] = $siteConfig->mail_additional_parameters;
                    TransportFactory::setConfig($type, $config);
                } else {
                    Log::error(__d('baser_core', 'メール送信の追加パラメータが不正なため無視しました。-f メールアドレス の形式で指定してください。'));
                }
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
     * @unitTest
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
     * @unitTest
     */
    public function deliver(string $content = ''): array
    {
        $this->dispatchLayerEvent('beforeDeliver');
        return parent::deliver($content);
    }


    /**
     * mail() の追加パラメータとして妥当か判定する
     *
     * エンベロープ送信者の指定「-f メールアドレス」（-f と アドレスの間の空白は任意）のみ許可する。
     *
     * @param string $params
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public static function isValidAdditionalParameters(string $params): bool
    {
        return (bool)preg_match('/\A-f ?[A-Za-z0-9._%+\-]+@[A-Za-z0-9.\-]+\.[A-Za-z]{2,}\z/', trim($params));
    }
}
