<?php
declare(strict_types=1);

use BaserCore\Database\Migration\BcSeed;

/**
 * EditorTemplates seed.
 */
class EditorTemplatesSeed extends BcSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeds is available here:
     * https://book.cakephp.org/phinx/0/en/seeding.html
     *
     * @return void
     */
    public function run(): void
    {
        $data = [
            [
                'id' => 1,
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
                'modified' => NULL,
                'created' => NULL,
            ],
            [
                'id' => 2,
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
                'modified' => NULL,
                'created' => NULL,
            ],
            [
                'id' => 3,
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
                'modified' => NULL,
                'created' => NULL,
            ],
        ];

        $table = $this->table('editor_templates');
        $table->insert($data)->save();
    }
}
