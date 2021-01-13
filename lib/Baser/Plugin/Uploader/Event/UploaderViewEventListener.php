<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Uploader.Event
 * @since           baserCMS v 3.0.10
 * @license         https://basercms.net/license/index.html
 */

/**
 * UploaderViewEventListener
 *
 * @package Uploader.Event
 * @property \BcHtmlHelper $BcHtml
 */
class UploaderViewEventListener extends BcViewEventListener
{

	public $events = ['afterLayout',
		'Pages.beforeRender'];

	/**
	 * afterLayout
	 *
	 * @return    void
	 * @access    pubic
	 */
	public function pagesBeforeRender(CakeEvent $event)
	{
		$event->data;
		$event->subject()->BcBaser;
	}

	public function afterLayout(CakeEvent $event)
	{

		$View = $event->subject();
		if (!BcUtil::isAdminSystem() || $View->name == 'CakeError') {
			return;
		}

		$this->BcHtml = $View->BcHtml;

		if (isset($View->BcCkeditor)) {

			if (preg_match_all("/(editor_[a-z0-9_]*?)\s*?=\s*?CKEDITOR\.replace/s", $View->output, $matches)) {

				/* ckeditor_uploader.js を読み込む */

				$jscode = $this->BcHtml->scriptBlock("var baseUrl ='" . $View->request->base . "/';");
				$jscode .= $this->BcHtml->scriptBlock("var adminPrefix ='" . Configure::read('Routing.prefixes.0') . "';");
				$jscode .= $this->BcHtml->i18nScript([
					'ckeditorTitle' => __d('baser', 'ファイルプロパティ'),
					'ckeditorAlertMessage1' => __d('baser', '画像を選択するか、URLを直接入力して下さい。'),
					'ckeditorInfoLabel' => __d('baser', 'イメージ情報'),
					'ckeditorInfoTitle' => __d('baser', 'イメージ情報'),
					'ckeditorDescriptionLabel' => __d('baser', '説明文'),
					'ckeditorCaptionLabel' => __d('baser', 'キャプション'),
					'ckeditorHspaceLabel' => __d('baser', '横間隔'),
					'ckeditorVspaceLabel' => __d('baser', '縦間隔'),
					'ckeditorAlignLabel' => __d('baser', '行揃え'),
					'ckeditorLeft' => __d('baser', '左'),
					'ckeditorAbsBottom' => __d('baser', '下部(絶対的)'),
					'ckeditorAbsMiddle' => __d('baser', '中央(絶対的)'),
					'ckeditorBaseline' => __d('baser', 'ベースライン'),
					'ckeditorBottom' => __d('baser', '下'),
					'ckeditorMiddle' => __d('baser', '中央'),
					'ckeditorRight' => __d('baser', '右'),
					'ckeditorTextTop' => __d('baser', 'テキスト上部'),
					'ckeditorTop' => __d('baser', '上'),
					'ckeditorSizeLabel' => __d('baser', 'サイズ'),
					'ckeditorOriginSize' => __d('baser', '元サイズ'),
					'ckeditorSmall' => __d('baser', '小'),
					'ckeditorMidium' => __d('baser', '中'),
					'ckeditorLarge' => __d('baser', '大'),
				], ['inline' => true]);
				$jscode .= $this->BcHtml->script('Uploader.admin/libs/ckeditor_uploader');
				$View->output = str_replace('</head>', $jscode . '</head>', $View->output);

				/* CSSを読み込む */
				// 適用の優先順位の問題があるので、bodyタグの直後に読み込む
				$css = $this->BcHtml->css('Uploader.uploader');
				$View->output = str_replace('</body>', $css . '</body>', $View->output);

				/* VIEWのCKEDITOR読込部分のコードを書き換える */
				foreach($matches[1] as $match) {
					$jscode = $this->__getCkeditorUploaderScript($match);
					$pattern = "/<script type=\"text\/javascript\">[\s\n]*?\/\/<\!\[CDATA\[[\s\n]*?([\$]\(window\)\.load.+?" . $match . "\s=\sCKEDITOR\.replace.*?)\/\/\]\]>\n*?<\/script>/s";
					$output = preg_replace($pattern, $this->BcHtml->scriptBlock("$1" . $jscode), $View->output);
					if (!is_null($output)) {
						$View->output = $output;
					}
				}

				/* 通常の画像貼り付けダイアログを画像アップローダーダイアログに変換する */
				$pattern = "/(CKEDITOR\.replace.*?\"toolbar\".*?)\"Image\"(.*?);/is";
				$View->output = preg_replace($pattern, "$1" . '"BaserUploader"' . "$2;", $View->output);

			}

		}

		if (!empty($View->request->params['prefix']) && $View->request->params['prefix'] == 'mobile') {

			/* モバイル画像に差し替える */
			$aMatch = "/<array([^>]*?)href=\"([^>]*?)\"([^>]*?)><img([^>]*?)\/><\/a>/is";
			$imgMatch = "/<img([^>]*?)src=\"([^>]*?)\"([^>]*?)\/>/is";
			$View->output = preg_replace_callback($aMatch, [$this, "__mobileImageAnchorReplace"], $View->output);
			$View->output = preg_replace_callback($imgMatch, [$this, "__mobileImageReplace"], $View->output);

		}

	}

	/**
	 * CKEditorのアップローダーを組み込む為のJavascriptを返す
	 *
	 * 「baserUploader」というコマンドを登録し、そのコマンドが割り当てられてボタンをツールバーに追加する
	 * {EDITOR_NAME}.addCommand    // コマンドを追加
	 * {EDITOR_NAME}.addButton    // ツールバーにボタンを追加
	 * ※ {EDITOR_NAME} は、コントロールのIDに変換する前提
	 *
	 * @return    string
	 * @access    private
	 */
	function __getCkeditorUploaderScript($id)
	{

		$css = $this->BcHtml->url('/uploader/css/ckeditor/contents.css');
		return <<< DOC_END
			$(window).load(function(){
				if(!(CKEDITOR.config.contentsCss instanceof Array)) {
					CKEDITOR.config.contentsCss = [CKEDITOR.config.contentsCss];
				}
				CKEDITOR.config.contentsCss.push('{$css}');
				{$id}.on( 'pluginsLoaded', function( ev ) {
					{$id}.addCommand( 'baserUploader', new CKEDITOR.dialogCommand( 'baserUploaderDialog' ));
					{$id}.ui.addButton( 'BaserUploader', { icon: 'image', label : 'アップローダー', command : 'baserUploader' });
				});
			});
DOC_END;

	}

	/**
	 * 画像タグをモバイル用に置き換える
	 *
	 * @param array $matches
	 * @return    string
	 * @access    private
	 */
	function __mobileImageReplace($matches)
	{

		$url = $matches[2];
		$pathinfo = pathinfo($url);

		if (!isset($pathinfo['extension'])) {
			return $matches[0];
		}

		$url = str_replace('__small', '', $url);
		$url = str_replace('__midium', '', $url);
		$url = str_replace('__large', '', $url);
		$basename = mb_basename($url, '.' . $pathinfo['extension']);
		$_url = 'files' . DS . 'uploads' . DS . $basename . '__mobile_small.' . $pathinfo['extension'];
		// TODO uploads固定となってしまっているのでmodelから取得するようにする
		$path = WWW_ROOT . $_url;

		$matches[1] = preg_replace('/width="[^"]+?"/', '', $matches[1]);
		$matches[1] = preg_replace('/height="[^"]+?"/', '', $matches[1]);
		$matches[1] = preg_replace('/style="[^"]+?"/', '', $matches[1]);
		$matches[3] = preg_replace('/width="[^"]+?"/', '', $matches[3]);
		$matches[3] = preg_replace('/height="[^"]+?"/', '', $matches[3]);
		$matches[3] = preg_replace('/style="[^"]+?"/', '', $matches[3]);

		if (file_exists($path)) {
			return '<img' . $matches[1] . 'src="' . $this->BcHtml->webroot($_url) . '"' . $matches[3] . '/>';
		} else {
			return $matches[0];
		}

	}

	/**
	 * アンカータグのリンク先が画像のものをモバイル用に置き換える
	 *
	 * @param array $matches
	 * @return    string
	 * @access    private
	 */
	function __mobileImageAnchorReplace($matches)
	{

		$url = $matches[2];
		$pathinfo = pathinfo($url);

		if (!isset($pathinfo['extension'])) {
			return $matches[0];
		}

		$url = str_replace('__small', '', $url);
		$url = str_replace('__midium', '', $url);
		$url = str_replace('__large', '', $url);
		$basename = mb_basename($url, '.' . $pathinfo['extension']);
		$_url = 'files' . DS . 'uploads' . DS . $basename . '__mobile_large.' . $pathinfo['extension'];
		// TODO uploads固定となってしまっているのでmodelから取得するようにする
		$path = WWW_ROOT . $_url;

		if (file_exists($path)) {
			return '<a' . $matches[1] . 'href="' . $this->BcHtml->webroot($_url) . '"' . $matches[3] . '><img' . $matches[4] . '/></a>';
		} else {
			return $matches[0];
		}

	}

}
