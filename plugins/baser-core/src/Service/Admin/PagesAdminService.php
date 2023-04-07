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

namespace BaserCore\Service\Admin;

use BaserCore\Service\PagesService;
use BaserCore\Service\SitesServiceInterface;
use BaserCore\Utility\BcContainerTrait;
use BaserCore\Utility\BcSiteConfig;
use BaserCore\Vendor\CKEditorStyleParser;
use Cake\Core\Configure;
use Cake\Datasource\EntityInterface;
use Cake\Utility\Inflector;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;

/**
 * PagesAdminService
 */
class PagesAdminService extends PagesService implements PagesAdminServiceInterface {

    /**
     * Trait
     */
    use BcContainerTrait;

    /**
     * 新規登録画面用の view 変数を取得する
     *
     * @param EntityInterface $page
     * @return array
     * @checked
     * @noTodo
     */
    public function getViewVarsForAdd(EntityInterface $page): array
    {
		// エディタオプション
		$editorOptions = ['editorDisableDraft' => true];
        $editorStyles = BcSiteConfig::get('editor_styles');
		if ($editorStyles) {
			$CKEditorStyleParser = new CKEditorStyleParser();
			$editorOptions = array_merge($editorOptions, [
				'editorStylesSet' => 'default',
				'editorStyles' => [
					'default' => $CKEditorStyleParser->parse($editorStyles)
				]
			]);
		}

		// ページテンプレートリスト
		$theme = [Inflector::camelize(Configure::read('BcApp.coreFrontTheme'))];
		$siteService = $this->getService(SitesServiceInterface::class);
		$site = $siteService->findById($page->content->site_id)->first();
		if (!empty($site) && $site->theme && $site->theme != $theme[0]) {
			$theme[] = $site->theme;
		}
		return [
            'page' => $page,
            'pageTemplateList' => $this->getPageTemplateList($page->content->id, $theme),
		    'editor' => BcSiteConfig::get('editor'),
            'editorOptions' => $editorOptions,
            'editorEnterBr' => BcSiteConfig::get('editor_enter_br')
		];
    }

    /**
     * 編集画面用の view 変数を取得する
     * @param EntityInterface $page
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getViewVarsForEdit(EntityInterface $page): array
    {
		// エディタオプション
		$editorOptions = ['editorDisableDraft' => false];
        $editorStyles = BcSiteConfig::get('editor_styles');
		if ($editorStyles) {
			$CKEditorStyleParser = new CKEditorStyleParser();
			$editorOptions = array_merge($editorOptions, [
				'editorStylesSet' => 'default',
				'editorStyles' => [
					'default' => $CKEditorStyleParser->parse($editorStyles)
				]
			]);
		}
		// ページテンプレートリスト
		$theme = [Inflector::camelize(Configure::read('BcApp.coreFrontTheme'))];
		$siteService = $this->getService(SitesServiceInterface::class);
		$site = $siteService->findById($page->content->site_id)->first();
		if (!empty($site) && $site->theme && $site->theme != $theme[0]) {
			$theme[] = $site->theme;
		}
		return [
            'page' => $page,
            'pageTemplateList' => $this->getPageTemplateList($page->content->id, $theme),
		    'editor' => BcSiteConfig::get('editor'),
            'editorOptions' => $editorOptions,
            'editorEnterBr' => BcSiteConfig::get('editor_enter_br')
		];
    }

}
