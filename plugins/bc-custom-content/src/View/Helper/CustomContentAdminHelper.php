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

use BaserCore\Error\BcException;
use BaserCore\Utility\BcUtil;
use BaserCore\View\Helper\BcAdminFormHelper;
use BcCustomContent\Model\Entity\CustomEntry;
use BcCustomContent\Model\Entity\CustomField;
use BcCustomContent\Model\Entity\CustomLink;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BcCustomContent\Model\Entity\CustomTable;
use BcCustomContent\Service\CustomEntriesServiceInterface;
use BcCustomContent\Service\CustomFieldsService;
use BcCustomContent\Service\CustomFieldsServiceInterface;
use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\Datasource\EntityInterface;

/**
 * CustomContentAdminHelper
 *
 * @property BcAdminFormHelper $BcAdminForm
 */
#[\AllowDynamicProperties]
class CustomContentAdminHelper extends CustomContentAppHelper
{

    /**
     * Helper
     * @var string[]
     */
    public array $helpers = ['BaserCore.BcBaser', 'BaserCore.BcAdminForm'];

    /**
     * 管理画面のエントリー一覧に表示するかどうか判定する
     *
     * @param CustomLink $customLink
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function isDisplayEntryList(CustomLink $customLink)
    {
        if (!$customLink->custom_field) return false;
        return ($this->isEnableField($customLink) && $customLink->display_admin_list);
    }

    /**
     * エントリー一覧のカラム数を取得する
     *
     * @param array $customLinks
     * @return int
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getEntryColumnsNum(array $customLinks)
    {
        $num = 6;   // id / title / status / published + creator_id / created + modified / アクション
        foreach($customLinks as $customLink) {
            /** @var CustomLink $customLink */
            if ($this->isDisplayEntryList($customLink)) $num++;
        }
        return $num;
    }

    /**
     * フィールドのラベルを取得する
     *
     * @param CustomLink $link
     * @return string
     * @checked
     * @unitTest
     */
    public function label(CustomLink $link, array $options = []): string
    {
        // TODO ucmitz label をプラグインに移行する
        if ($link->custom_field->type === 'BcCcTextarea' && $link->parent_id) {
            return $this->BcAdminForm->label($this->getFieldName($link, $options), $link->title, $options) . '<br>';
        } else {
            return $this->BcAdminForm->label($this->getFieldName($link, $options), $link->title, $options);
        }
    }

    /**
     * ループを考慮したフィールド名を取得する
     *
     * @param CustomLink $link
     * @param array $options
     * @return string
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getFieldName(CustomLink $link, array $options = [])
    {
        $options = array_merge([
            'fieldName' => null,
        ], $options);

        if ($options['fieldName']) {
            return $options['fieldName'];
        } else {
            return $link->name;
        }
    }

    /**
     * フィールドの必須マークを取得する
     *
     * @param CustomLink $link
     * @return string
     * @checked
     * @noTodo
     * @unitTest
     */
    public function required(CustomLink|EntityInterface $link): string
    {
        if (!$link->children) {
            return ($link->required)? $this->BcBaser->getElement('BcCustomContent.required') : '';
        } else {
            $hasRequired = false;
            foreach($link->children as $child) {
                if ($child->required) $hasRequired = true;
            }
            return ($hasRequired)? $this->BcBaser->getElement('BcCustomContent.required') : '';
        }
    }

    /**
     * フィールドのコントロールを取得する
     *
     * @param CustomLink $link
     * @param array $options
     * @return string
     * @checked
     * @noTodo
     * @unitTest
     */
    public function control(CustomLink $customLink, array $options = []): string
    {
        $options = array_merge_recursive(BcUtil::pairToAssoc($customLink->options), [
        ], $options);

        if ($customLink->class) $options['class'] = $customLink->class;

        if (!$customLink->custom_field) return '';
        /** @var CustomField $field */
        $field = $customLink->custom_field;

        $tmpName = $customLink->name;
        $customLink->name = $this->getFieldName($customLink, $options);

        $out = '';
        if (method_exists($this->{$field->type}, 'control')) {
            $out = $this->{$field->type}->control($customLink, $options);
        }

        $customLink->name = $tmpName;
        return $out;
    }

    /**
     * フィールドのコントロールを取得する
     *
     * @param CustomField $field
     * @param array $options
     * @return string
     * @checked
     * @noTodo
     * @unitTest
     */
    public function preview(string $fieldName, string $type, CustomField $field): string
    {
        if(is_null($this->{$type})) {
            throw new BcException(__d('baser_core', 'ヘルパー "{0}Helper" を定義してください', $type));
        }
        if (method_exists($this->{$type}, 'preview')) {
            return $this->{$type}->preview(new CustomLink([
                'name' => $fieldName,
                'custom_field' => $field
            ]));
        }
        return '';
    }

    /**
     * エラー表示を取得
     *
     * @param CustomLink $link
     * @param array $options
     * @return string
     * @checked
     * @noTodo
     * @unitTest
     */
    public function error(CustomLink $link, $options = []): string
    {
        $options = array_merge([
            'loop' => false,
            'parent' => null,
            'index' => null
        ], $options);

        if ($options['parent'] && $options['parent']->group_valid) {
            return '';
        } else {
            return $this->BcAdminForm->error($this->getFieldName($link, $options));
        }
    }

    /**
     * 説明文を表示する
     *
     * ヘルプ用のツールチップで表示する
     *
     * @param CustomLink $link
     * @return string
     * @checked
     * @noTodo
     * @unitTest
     */
    public function description(CustomLink $link)
    {
        if ($link->description) {
            return '<i class="bca-icon--question-circle bca-help"></i>' .
                '<div class="bca-helptext">' .
                preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $link->description) .
                '</div>';
        }
        return '';
    }

    /**
     * 前見出しを表示する
     *
     * @param CustomLink $link
     * @return string
     * @checked
     * @noTodo
     * @unitTest
     */
    public function beforeHead(CustomLink $link): string
    {
        if ($link->before_head) {
            return h($link->before_head) . '&nbsp;';
        }
        return '';
    }

    /**
     * 後見出しを表示する
     *
     * @param CustomLink $link
     * @return string
     * @checked
     * @noTodo
     * @unitTest
     */
    public function afterHead(CustomLink $link): string
    {
        if ($link->after_head) {
            return '&nbsp;' . h($link->after_head);
        }
        return '';
    }

    /**
     * 注意書きを表示する
     *
     * @param CustomLink $link
     * @return string
     * @checked
     * @noTodo
     * @unitTest
     */
    public function attention(CustomLink $link): string
    {
        if ($link->attention) {
            return '<div class="bca-attention"><small>' .
                h($link->attention) .
                '</small></div>';
        }
        return '';
    }

    /**
     * カスタムエントリー一覧のタイトルを取得する
     *
     * ツリー構造テーブルの場合、見出しフィールドが title に設定されている場合に、リンクを設定する
     *
     * @param CustomTable $table
     * @param CustomEntry $entry
     * @return string
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getEntryIndexTitle(CustomTable $table, CustomEntry $entry)
    {
        if ($table->has_child || $entry->custom_table->display_field === 'title') {
            return $this->BcBaser->getLink(
                $entry->title,
                ['action' => 'edit', $table->id, $entry->id],
                ['escape' => true]
            );
        } else {
            return h($entry->title);
        }
    }

    /**
     * プラグインのメタフィールドを表示する
     * @checked
     * @noTodo
     * @unitTest
     */
    public function displayPluginMeta()
    {
        $fieldTypes = Configure::read('BcCustomContent.fieldTypes');
        foreach($fieldTypes as $key => $value) {
            if ($key === 'group') continue;
            $element = 'custom_field_meta';
            if (file_exists(Plugin::templatePath($key) . 'Admin/element/' . DS . $element . '.php')) {
                $this->BcBaser->element($key . '.' . $element);
            }
        }
    }

    /**
     * カスタムエントリーを上に移動できるか判定
     *
     * @param \ArrayObject $entries
     * @param CustomEntry $currentEntry
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function isEnabledMoveUpEntry(\ArrayObject $entries, CustomEntry $currentEntry)
    {
        $checkOn = false;
        foreach($entries as $key => $entry) {
            if ($currentEntry->level !== $entry->level) continue;
            /** @var CustomEntry $entry */
            if ($entry->id === $currentEntry->id) {
                $checkOn = true;
            }
            if (!$checkOn) return true;
        }
        return false;
    }

    /**
     * カスタムエントリーを下に移動できるか判定
     *
     * @param \ArrayObject $entries
     * @param CustomEntry $currentEntry
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function isEnabledMoveDownEntry(\ArrayObject $entries, CustomEntry $currentEntry)
    {
        $checkOn = false;
        foreach($entries as $entry) {
            if ($currentEntry->level !== $entry->level) continue;
            if ($checkOn) return true;
            /** @var CustomEntry $entry */
            if ($entry->id === $currentEntry->id) {
                $checkOn = true;
            }
        }
        return false;
    }

    /**
     * カスタムエントリーが公開状態かどうか判定
     *
     * @param CustomEntry $entry
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function isAllowPublishEntry(CustomEntry $entry)
    {
        $entriesService = $this->getService(CustomEntriesServiceInterface::class);
        return $entriesService->isAllowPublish($entry);
    }

    /**
     * カスタムフィールドの一覧を取得する
     *
     * @return \Cake\ORM\Query
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getFields()
    {
        /** @var CustomFieldsService $fieldsService */
        $fieldsService = $this->getService(CustomFieldsServiceInterface::class);
        return $fieldsService->getIndex();
    }

    /***
     * グループのエラーを取得する
     *
     * @param CustomLink $link
     * @return string
     * @checked
     * @noTodo
     */
    public function getGroupErrors(CustomLink $link)
    {
        $errors = [];
        if ($link->group_valid && $link->children) {
            foreach($link->children as $child) {
                if ($this->BcAdminForm->isFieldError($child->name)) {
                    $errors[] = $this->error($child);
                }
            }
        }
        return implode("\n", $errors);
    }

}
