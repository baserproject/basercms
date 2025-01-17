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
use BaserCore\Model\Entity\Content;
use BaserCore\Utility\BcContainerTrait;
use BaserCore\View\Helper\BcBaserHelper;
use BcCustomContent\Model\Entity\CustomEntry;
use BcCustomContent\Model\Entity\CustomField;
use BcCustomContent\Model\Entity\CustomLink;
use BcCustomContent\Service\CustomEntriesServiceInterface;
use BcCustomContent\Utility\CustomContentUtil;
use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\View\Helper;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use Cake\View\View;

/**
 * CustomContentAppHelper
 *
 * @property BcBaserHelper $BcBaser
 */
#[\AllowDynamicProperties]
class CustomContentAppHelper extends Helper
{

    /**
     * Trait
     */
    use BcContainerTrait;

    /**
     * Helper
     * @var string[]
     */
    public array $helpers = [
        'BaserCore.BcBaser'
    ];

    /**
     * Constructor
     *
     * @param View $view
     * @param array $config
     * @checked
     * @noTodo
     * @unitTest
     */
    public function __construct(View $view, array $config = [])
    {
        parent::__construct($view, $config);
        $this->loadPluginHelper();
    }

    /**
     * プラグインのヘルパーを読み込む
     * @checked
     * @noTodo
     * @unitTest
     */
    public function loadPluginHelper(): void
    {
        $fieldTypes = Configure::read('BcCustomContent.fieldTypes');
        if (!$fieldTypes) return;
        foreach($fieldTypes as $plugin => $type) {
            if ($plugin === 'group') continue;
            $pluginPath = Plugin::path($plugin);
            if (file_exists($pluginPath . 'src' . DS . 'View' . DS . 'Helper' . DS . $plugin . 'Helper.php')) {
                $this->{$plugin} = $this->_View->loadHelper(
                    "$plugin",
                    ['className' => "$plugin.$plugin"]
                );
            } else {
                throw new BcException(__d('baser_core', 'ヘルパー "{0}Helper" を定義してください', $plugin));
            }
        }
    }

    /**
     * フィールドが有校かどうか判定する
     *
     * グループフィールドで子がいない場合は無効とする
     *
     * @param CustomLink $customLink
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function isEnableField(CustomLink $customLink): bool
    {
        if (!$customLink->custom_field) return false;
        if ($customLink->custom_field->type === 'group' && empty($customLink->children)) return false;
        return $customLink->status;
    }

    /**
     * カスタムエントリーのURLを取得する
     *
     * @param CustomEntry $entry
     * @param bool $base
     * @return mixed|string
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getEntryUrl(CustomEntry $entry, $full = true)
    {
        $content = $this->getView()->getRequest()->getAttribute('currentContent');
        if(!$content) return false;
        /** @var CustomEntriesServiceInterface $entriesService */
        $entriesService = $this->getService(CustomEntriesServiceInterface::class);
        return $entriesService->getUrl($content, $entry, $full);
    }

    /**
     * 検索コントロールを取得
     *
     * @param CustomLink $customLink
     * @param array $options
     * @return string
     * @checked
     * @noTodo
     * @unitTest
     */
    public function searchControl(CustomLink $customLink, array $options = []): string
    {
        if (!$customLink->custom_field) return '';
        /** @var CustomField $field */
        $field = $customLink->custom_field;

        if (method_exists($this->{$field->type}, 'searchControl')) {
            return $this->{$field->type}->searchControl($customLink, $options);
        }
        return '';
    }

    /**
     * エントリー一覧の検索に表示するかどうか判定する
     *
     * @param CustomLink $customLink
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function isDisplayEntrySearch(CustomLink $customLink, string $type = 'front')
    {
        if($type === 'front') {
            $isTarget = $customLink->search_target_front;
        }elseif($type === 'admin') {
            $isTarget = $customLink->search_target_admin;
        } else {
            return false;
        }
        if (!$customLink->custom_field) return false;
        if ($customLink->custom_field->type === 'group' && empty($customLink->children)) return false;
        // ファイルはスキップ
        if(CustomContentUtil::getPluginSetting($customLink->custom_field->type, 'controlType') === 'file') return false;
        return ($customLink->status && $isTarget);
    }

}
