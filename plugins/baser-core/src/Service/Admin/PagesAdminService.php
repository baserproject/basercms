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

use BaserCore\Error\BcException;
use BaserCore\Model\Entity\Site;
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
     * @UnitTest
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
		// テーマが「メインサイトに従う」の場合は親サイトをたどって適用テーマを利用する
		$appliedTheme = $site? $this->getAppliedTheme($site) : null;
		if ($appliedTheme && $appliedTheme != $theme[0]) {
			$theme[] = $appliedTheme;
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
		// テーマが「メインサイトに従う」の場合は親サイトをたどって適用テーマを利用する
		$appliedTheme = $site? $this->getAppliedTheme($site) : null;
		if ($appliedTheme && $appliedTheme != $theme[0]) {
			$theme[] = $appliedTheme;
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
	 * サイトに適用されているテーマを取得する
	 *
	 * 自身に設定されていない場合は親サイトをたどって取得する。見つからない場合は null
	 *
	 * @param Site $site
	 * @return string|null
	 * @checked
	 * @noTodo
	 * @unitTest
	 */
	protected function getAppliedTheme(Site $site): ?string
	{
		try {
			return $site->getAppliedTheme();
		} catch (BcException) {
			return null;
		}
	}

}
