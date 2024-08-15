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

namespace BcCustomContent\View\Helper;

use BaserCore\Utility\BcContainerTrait;
use BaserCore\Utility\BcUtil;
use BaserCore\View\Helper\BcTimeHelper;
use BcCustomContent\Model\Entity\CustomContent;
use BcCustomContent\Model\Entity\CustomEntry;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BcCustomContent\Model\Entity\CustomField;
use BcCustomContent\Service\CustomLinksService;
use BcCustomContent\Service\CustomLinksServiceInterface;
use Cake\Utility\Hash;

/**
 * CustomContentHelper
 *
 * @property BcTimeHelper $BcTime
 */
#[\AllowDynamicProperties]
class CustomContentHelper extends CustomContentAppHelper
{

    /**
     * Trait
     */
    use BcContainerTrait;

    /**
     * ヘルパ
     * @var string[]
     */
    public array $helpers = [
        'BaserCore.BcBaser',
        'BaserCore.BcTime'
    ];

    /**
     * カスタムリンクのキャッシュ
     *
     * CustomContentHelper::getLinks() で取得、保存
     * CustomContentHelper::clearCacheLinks() で削除
     *
     * @var array
     */
    private $cacheLinks = [];

    /**
     * カスタムコンテンツのタイトルを取得する
     *
     * @return string
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getTitle()
    {
        return $this->_View->getRequest()->getAttribute('currentContent')->title;
    }

    /**
     * カスタムコンテンツのタイトルを出力する
     * @checked
     * @noTodo
     * @unitTest ラッパーメソッドに付きテスト不要
     */
    public function title(): void
    {
        echo $this->getTitle();
    }

    /**
     * カスタムコンテンツに説明文が存在するか判定する
     *
     * @param CustomContent $content
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function descriptionExists(CustomContent $content)
    {
        return (bool)$content->description;
    }

    /**
     * カスタムコンテンツの説明文を取得する
     *
     * @param CustomContent $content
     * @return string
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getDescription(CustomContent $content)
    {
        return $content->description;
    }

    /**
     * カスタムコンテンツの説明文を出力する
     *
     * @param CustomContent $content
     * @checked
     * @noTodo
     */
    public function description(CustomContent $content)
    {
        echo $this->getDescription($content);
    }

    /**
     * カスタムエントリーのタイトルを取得する
     *
     * @param CustomEntry $entry
     * @return string
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getEntryTitle(CustomEntry $entry)
    {
        return $entry->title;
    }

    /**
     * カスタムエントリーのタイトルを週ty録する
     *
     * @param CustomEntry $entry
     * @param array $options
     *  - `link`: 詳細ページへのリンクタグとして出力するかどうか（初期値：true）
     * @checked
     * @noTodo
     */
    public function entryTitle(CustomEntry $entry, array $options = [])
    {
        $options = array_merge([
            'link' => true
        ], $options);
        if ($options['link']) {
            $this->BcBaser->link(
                $this->getEntryTitle($entry),
                $this->getEntryUrl($entry, false),
            );
        } else {
            echo h($this->getEntryTitle($entry));
        }
    }

    /**
     * カスタムエントリーの公開日を取得する
     *
     * @param CustomEntry $entry
     * @return string
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getPublished(CustomEntry $entry)
    {
        return $this->BcTime->format($entry->created);
    }

    /**
     * カスタムエントリーの公開日を出力する
     *
     * @param CustomEntry $entry
     * @checked
     * @noTodo
     * @unitTest ラッパーメソッドに付きテスト不要
     */
    public function published(CustomEntry $entry)
    {
        echo $this->getPublished($entry);
    }

    /**
     * カスタムエントリーのフィールドのタイトルを取得する
     *
     * @param CustomEntry|array $entry
     * @param string $fieldName
     * @return array|bool|float|int|mixed|string|null
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getFieldTitle(mixed $entry, string $fieldName)
    {
        if (is_array($entry)) $entry = new CustomEntry($entry);
        $customLink = $this->getLink($entry->custom_table_id, $fieldName);
        return h($customLink->title);
    }

    /**
     * カスタムエントリーのフィールドの値を取得する
     *
     * @param CustomEntry|array $entry
     * @param string $fieldName
     * @param array $options
     * @return string|array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getFieldValue(mixed $entry, string $fieldName, array $options = [])
    {
        if (is_array($entry)) $entry = new CustomEntry($entry);
        $options = array_merge([
            'beforeHead' => true,
            'afterHead' => true,
            'beforeLinefeed' => true,
            'afterLinefeed' => true,
            'entity' => $entry
        ], $options);

        $customLink = $this->getLink($entry->custom_table_id, $fieldName);

        if (empty($customLink->display_front)) return '';
        /** @var CustomField $field */
        $field = $customLink->custom_field;

        if ($field->type === 'group') return $entry->{$fieldName};

        if (method_exists($this->{$field->type}, 'get')) {
            $out = $this->{$field->type}->get($entry->{$fieldName}, $customLink, $options);
            if (BcUtil::isAdminSystem()) {
                if ($options['beforeHead'] && $customLink->before_head) $out = "{$customLink->before_head}&nbsp;{$out}";
                if ($options['beforeLinefeed'] && $customLink->before_linefeed) $out = "<br>{$out}";
                if ($options['afterHead'] && $customLink->after_head) $out = "{$out}&nbsp;{$customLink->after_head}";
                if ($options['afterLinefeed'] && $customLink->after_linefeed) $out = "$out<br>";
            }
            return $out;
        }
        return '';
    }

    /**
     * 関連リンクのエンティティを取得する
     *
     * @param int $tableId
     * @param string $fieldName
     * @return false|mixed
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getLink(int $tableId, string $fieldName)
    {
        if (!$this->cacheLinks) {
            $links = $this->getLinks($tableId, false)->toArray();
            $arrayLinks = array_combine(Hash::extract($links, '{n}.name'), array_values($links));
            $this->cacheLinks = $arrayLinks;
        }
        if (isset($this->cacheLinks[$fieldName])) {
            return $this->cacheLinks[$fieldName];
        }
        return false;
    }

    /**
     * Get Field
     * @param int $tableId
     * @param string $fieldName
     * @return false
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getField(int $tableId, string $fieldName)
    {
        $link = $this->getLink($tableId, $fieldName);
        if (!$link || empty($link->custom_field)) return false;
        return $link->custom_field;
    }

    /**
     * Is Loop
     * @param CustomEntry $customEntry
     * @param string $fieldName
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function isLoop(CustomEntry $customEntry, string $fieldName): bool
    {
        $link = $this->getLink($customEntry->custom_table_id, $fieldName);
        if (!$link || empty($link->custom_field)) return false;
        return ($link->use_loop && $link->custom_field->type === 'group');
    }

    /**
     * 関連リンクのエンティティリストを取得する
     *
     * @param int $tableId
     * @param bool $isThreaded
     * @return mixed
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getLinks(int $tableId, bool $isThreaded = true)
    {
        if ($isThreaded) {
            $options = ['finder' => 'threaded', 'contain' => ['CustomFields', 'CustomTables']];
        } else {
            $options = ['finder' => 'all', 'contain' => ['CustomFields', 'CustomTables']];
        }
        $options['status'] = true;
        /** @var CustomLinksService $linksService */
        $linksService = $this->getService(CustomLinksServiceInterface::class);
        $links = $linksService->getIndex($tableId, $options)->all();
        return $links;
    }

    /**
     * カスタムリンクの子を取得する
     *
     * @param CustomEntry $customEntry
     * @param string $fieldName
     * @return \Cake\Datasource\ResultSetInterface
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getLinkChildren(CustomEntry $customEntry, string $fieldName)
    {
        $link = $this->getLink($customEntry->custom_table_id, $fieldName);
        /** @var CustomLinksService $linksService */
        $linksService = $this->getService(CustomLinksServiceInterface::class);
        return $linksService->getIndex($customEntry->custom_table_id, [
            'finder' => 'children',
            'for' => $link->id
        ])->all();
    }

    /**
     * 関連リンクのエンティティキャッシュを削除する
     * @checked
     * @noTodo
     * @unitTest
     */
    public function clearCacheLinks()
    {
        $this->cacheLinks = [];
    }

    /**
     * カスタムエントリーのフィールドについて表示対象かどうか判定する
     *
     * @param CustomEntry $entry
     * @param string $fieldName
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function isDisplayField(CustomEntry $entry, string $fieldName)
    {
        $customLink = $this->getLink($entry->custom_table_id, $fieldName);
        return (bool)$customLink->display_front;
    }

}
