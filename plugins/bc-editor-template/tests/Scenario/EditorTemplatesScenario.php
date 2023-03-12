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

namespace BcEditorTemplate\Test\Scenario;

use BcEditorTemplate\Test\Factory\EditorTemplateFactory;
use CakephpFixtureFactories\Scenario\FixtureScenarioInterface;

/**
 * 利用する場合は、テーブルの初期化に次のフィクスチャの定義が必要
 * - plugin.BcEditorTemplate.Factory/EditorTemplates
 */
class EditorTemplatesScenario implements FixtureScenarioInterface
{

    /**
     * load
     */
    public function load(...$args)
    {
        EditorTemplateFactory::make([
            'id' => '1',
            'name' => '画像（左）とテキスト',
            'image' => 'template1.gif',
            'description' => '画像を左に配置し、その右にテキストを配置するブロックです。',
            'html' => '<div class="template-image-float-left clearfix">
	<div class="image">ここに画像を挿入します</div>
	<div class="text">
		<h2>見出しを挿入します。</h2>
		<p>1段落目のテキストを挿入します。</p>
		<p>2段落目のテキストを挿入します。</p>
	</div>
</div>
<p>新しいブロックを挿入します。不要な場合はこの段落を削除します</p>',
            'modified' => null,
            'created' => '2015-01-27 12:56:52'
        ])->persist();
        EditorTemplateFactory::make([
            'id' => '2',
            'name' => '画像（右）とテキスト',
            'image' => 'template2.gif',
            'description' => '画像を右に配置し、その左にテキストを配置するブロックです。',
            'html' => '<div class="template-image-float-right clearfix">
	<div class="image">ここに画像を挿入します</div>
	<div class="text">
		<h2>見出しを挿入します。</h2>
		<p>1段落目のテキストを挿入します。</p>
		<p>2段落目のテキストを挿入します。</p>
	</div>
</div>
<p>新しいブロックを挿入します。不要な場合はこの段落を削除します</p>',
            'modified' => null,
            'created' => '2015-01-27 12:56:52'
        ])->persist();
        EditorTemplateFactory::make([
            'id' => '3',
            'name' => 'テキスト２段組',
            'image' => 'template3.gif',
            'description' => 'テキストを左右に２段組するブロックです。',
            'html' => '<div class="template-two-block clearfix">
	<div class="block-left">
		<h2>
			見出しを挿入します。</h2>
		<p>
			1段落目のテキストを挿入します。</p>
		<p>
			2段落目のテキストを挿入します。</p>
	</div>
	<div class="block-right">
		<h2>
			見出しを挿入します。</h2>
		<p>
			1段落目のテキストを挿入します。</p>
		<p>
			2段落目のテキストを挿入します。</p>
	</div>
</div>
<p>
	新しいブロックを挿入します。不要な場合はこの段落を削除します</p>',
            'modified' => null,
            'created' => '2015-01-27 12:56:52'
        ])->persist();
    }
}
