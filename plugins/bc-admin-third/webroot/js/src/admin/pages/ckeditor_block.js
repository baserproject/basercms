// $jscode = "var editor_" . $field . ";";
window.onload = function() {
    alert(editor_contents_tmp + CKEDITOR.version);
};

// $jscode .= "$(window).load(function(){";
// if (!$this->inited) {
//     $jscode .= "CKEDITOR.addStylesSet('basercms'," . json_encode(($this->style)) .");";
//     $this->inited = true;
// } else {
//     $jscode .= '';
// }
// if (!$this->_initedStyles && $editorStyles) {
//     foreach($editorStyles as $key => $style) {
//         $jscode .= "CKEDITOR.addStylesSet('" . $key . "'," . json_encode(($style)) .");";
//     }
//     $this->_initedStyles = true;
// }

// if ($editorUseTemplates) {
//     $jscode .= "CKEDITOR.config.templates_files = [ '" . $this->url(['controller' => 'editor_templates', 'action' => 'js']) . "' ];";
// }
// $jscode .= "CKEDITOR.config.allowedContent = true;";
// $jscode .= "CKEDITOR.config.extraPlugins = 'draft,showprotected';";
// $jscode .= "CKEDITOR.config.stylesCombo_stylesSet = '" . $editorStylesSet . "';";
// $jscode .= "CKEDITOR.config.protectedSource.push( /<\?[\s\S]*?\?>/g );";
// $jscode .= 'CKEDITOR.dtd.$removeEmpty["i"] = false;'; //　空「i」タグを消さないようにする
// $jscode .= 'CKEDITOR.dtd.$removeEmpty["span"] = false;'; //　空「span」タグを消さないようにする

// if ($editorEnterBr) {
//     $jscode .= "CKEDITOR.config.enterMode = CKEDITOR.ENTER_BR;";
// }

// // $this->webroot で、フロントテーマのURLを取得できるようにするため、
// // 一旦テーマをフロントのテーマに切り替える
// $theme = $this->theme;
// $theme = Configure::read('BcSite.theme');
// if ($theme) {
//     $this->theme = $theme;
// }

// $themeEditorCsses = [];
// if ($theme) {
//     $themeEditorCsses[] = [
//         'path' => BASER_THEMES . Configure::read('BcSite.theme') . DS . 'css' . DS . 'editor.css',
//         'url' => $this->webroot('/css/editor.css')
//     ];
// }
// $themeEditorCsses[] = [
//     'path' => BASER_VIEWS . 'webroot' . DS . 'css' . DS . 'admin' . DS . 'ckeditor' . DS . 'contents.css',
//     'url' => $this->webroot('/css/admin/ckeditor/contents.css')
// ];

// if ($theme) {
//     $sitePrefix = '';
//     if (!empty($this->request->getData('Site.name'))) {
//         $sitePrefix = $this->request->getData('Site.name');
//     }
//     if ($sitePrefix) {
//         array_unshift($themeEditorCsses, [
//             'path' => BASER_THEMES . Configure::read('BcSite.theme') . DS . 'css' . DS . $sitePrefix . DS . 'editor.css',
//             'url' => $this->webroot('/css/' . $sitePrefix . '/editor.css')
//         ]);
//     }
// }

// $this->theme = $theme;

// foreach($themeEditorCsses as $themeEditorCss) {
//     if (file_exists($themeEditorCss['path'])) {
//         $jscode .= "CKEDITOR.config.contentsCss = ['" . $themeEditorCss['url'] . "'];";
//         break;
//     }
// }

// $jscode .= "editor_" . $field . " = CKEDITOR.replace('" . $domId . "'," . json_encode(($options)) .");";
// $jscode .= "editor_{$field}.on('pluginsLoaded', function(event) {";
// if ($editorUseDraft) {
//     if ($draftAreaId) {
//         $jscode .= "editor_{$field}.draftDraftAreaId = '{$draftAreaId}';";
//     }
//     if ($publishAreaId) {
//         $jscode .= "editor_{$field}.draftPublishAreaId = '{$publishAreaId}';";
//     }
//     if ($editorReadOnlyPublish) {
//         $jscode .= "editor_{$field}.draftReadOnlyPublish = true;";
//     }
// }

// $jscode .= " });";
// $draftMode = 'publish';
// $fieldCamelize = Inflector::camelize($field);
// if ($editorUseDraft) {
//     $jscode .= "editor_{$field}.on('instanceReady', function(event) {";
//     if ($editorDisableDraft) {
//         $jscode .= "editor_{$field}.execCommand('changePublish');";
//         $jscode .= "editor_{$field}.execCommand('disableDraft');";
//     }
//     if ($editorDisablePublish) {
//         $jscode .= "editor_{$field}.execCommand('changeDraft');";
//         $jscode .= "editor_{$field}.execCommand('disablePublish');";
//         $draftMode = 'draft';
//     }
//     $jscode .= <<< EOL
// editor_{$field}.on( 'beforeCommandExec', function( ev ){
// if(ev.data.name === 'changePublish' || ev.data.name === 'copyPublish') {
//     $("#DraftMode{$fieldCamelize}").val('publish');
// } else if(ev.data.name === 'changeDraft' || ev.data.name === 'copyDraft') {
//     $("#DraftMode{$fieldCamelize}").val('draft');
// }
// });
// EOL;
//     $jscode .= " });";
// }

// $jscode .= "editor_{$field}.on('instanceReady', function(event) {";
// $jscode .= "if(editor_{$field}.getCommand('maximize').uiItems.length > 0) {";

// // ツールバーの表示を切り替え
// $jscode .= <<< EOL
// editor_{$field}.getCommand('maximize').on( 'state' , function( e )
// {
// if(this.state == 1) {
//     $("#ToolBar").hide();
// } else {
//     $("#ToolBar").show();
// }
// });
// EOL;

// $jscode .= "}";
// $jscode .= " });";
// $jscode .= "});";
