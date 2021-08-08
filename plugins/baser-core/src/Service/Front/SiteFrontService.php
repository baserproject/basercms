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

namespace BaserCore\Service\Front;

use BaserCore\Model\Entity\Site;
use BaserCore\Service\SiteConfigsTrait;
use BaserCore\Service\SitesService;
use BaserCore\Utility\BcAbstractDetector;
use BaserCore\Utility\BcAgent;
use BaserCore\Utility\BcLang;
use BaserCore\Utility\BcUtil;
use Cake\Datasource\EntityInterface;
use Cake\Http\ServerRequest;
use Cake\Routing\Router;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * SiteFrontService
 */
class SiteFrontService extends SitesService
{

    use SiteConfigsTrait;

    /**
     * 現在のURLからサイトを取得する
     *
     * @param bool $direct
     * @return Site|null
     * @checked
     * @noTodo
     * @unitTest
     */
    public function findCurrent($direct = true): ?Site
    {
        $request = Router::getRequest();
        if (!$request) {
            $request = new ServerRequest();
        }
        $url = $request->getPath();
        $sites = $this->Sites->find()->all();
        if (!$sites) {
            return null;
        }
        $url = preg_replace('/^\//', '', $url);
        $currentSite = null;
        foreach($sites as $site) {
            if ($site->alias) {
                $domainKey = '';
                if ($site->use_subdomain) {
                    if ($site->domain_type == 1) {
                        $domainKey = BcUtil::getSubDomain() . '/';
                    } elseif ($site->domain_type == 2) {
                        $domainKey = BcUtil::getCurrentDomain() . '/';
                    }
                }
                $regex = '/^' . preg_quote($site->alias, '/') . '\//';
                if (preg_match($regex, $domainKey . $url)) {
                    $currentSite = $site;
                    break;
                }
            }
        }
        if (!$currentSite) {
            $currentSite = $sites->first();
        }
        if (!$direct) {
            $subSite = $this->findCurrentSub(true);
            if ($subSite) {
                $currentSite = $subSite;
            }
        }
        return $currentSite;
    }

    /**
     * 現在のサイトに関連するメインサイトを取得
     *
     * @return Site|null
     */
    public function findCurrentMain(): ?EntityInterface
    {
        $currentSite = $this->findCurrent();
        if($currentSite->main_site_id) {
            return $this->Sites->find()->where(['id' => $currentSite->main_site_id])->first();
        }
        return null;
    }

    /**
     * 現在のサイトとユーザーエージェントに関連するサイトを取得する
     *
     * @param bool $sameMainUrl
     * @param BcAbstractDetector $detector
     * @param bool $sameMainUrl
     * @return Site|null
     */
    public function findCurrentSub($sameMainUrl = false, BcAbstractDetector $agent = null, $lang = null)
    {

        $currentSite = $this->findCurrent();
        $sites = $this->Sites->find()->all();

        if (!$lang) {
            $lang = BcLang::findCurrent();
        }
        if (!$agent) {
            $agent = BcAgent::findCurrent();
        }

        // 言語の一致するサイト候補に絞り込む
        $langSubSites = [];
        if ($lang && $this->getSiteConfig('use_site_lang_setting')) {
            foreach($sites as $site) {
                if (!$site->status) {
                    continue;
                }
                if (!$sameMainUrl || ($sameMainUrl && $site->same_main_url)) {
                    if ($site->lang == $lang->name && $currentSite->id == $site->main_site_id) {
                        $langSubSites[] = $site;
                        break;
                    }
                }
            }
        }
        if ($langSubSites) {
            $subSites = $langSubSites;
        } else {
            $subSites = $sites;
        }
        if ($agent && $this->getSiteConfig('use_site_device_setting')) {
            foreach($subSites as $subSite) {
                if (!$subSite->status) {
                    continue;
                }
                if (!$sameMainUrl || ($sameMainUrl && $subSite->same_main_url)) {
                    if ($subSite->device == $agent->name && $currentSite->id == $subSite->main_site_id) {
                        return $subSite;
                    }
                }
            }
        }
        if ($langSubSites) {
            return $langSubSites[0];
        }
        return null;
    }
}
