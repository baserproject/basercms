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

namespace BcCcBurgerEditor\View\Helper;

use BcCustomContent\Model\Entity\CustomLink;
use Cake\View\Helper;

/**
 * Class BcCcBurgerEditorHelper
 */
#[\AllowDynamicProperties]
class BcCcBurgerEditorHelper extends Helper
{

    /**
     * helpers
     *
     * @var array
     */

    protected array $helpers = [
        'BaserCore.BcAdminForm' => ['templates' => 'BaserCore.bc_form']
    ];

    /**
     * control
     *
     * @param string $fieldName
     * @param array $options
     * @return string
     */
    public function control($field, array $options = []): string
    {
        $useDraft = !empty($field->custom_field->meta['BcCcBurgerEditor']['editor_use_draft']);
        $editorOptions = [
            'type' => 'editor',
            'editor' => "BcBurgerEditor.BurgerEditor",
            'editorUseDraft' => $useDraft,
            'editorWidth' => 'auto',
            'editorHeight' => '480px',
            'editorEnterBr' => 0,
        ];
        // 下書きを利用する場合のみ、下書き保存用フィールド（{フィールド名}_draft）を渡す
        if ($useDraft) {
            $editorOptions['editorDraftField'] = $field->name . '_draft';
        }
        return $this->BcAdminForm->editor($field->name, $editorOptions);
    }

    /**
     * Search Control
     *
     * @param CustomLink $link
     * @param array $options
     * @return string
     */
    public function searchControl(CustomLink $link, array $options = []): string
    {
        $options = array_merge([
            'type' => 'text'
        ], $options);
        return $this->BcAdminForm->control($link->name, $options);
    }

    /**
     * Get
     *
     * @param mixed $fieldValue
     * @param CustomLink $link
     * @param array $options
     * @return string
     */
    public function get($fieldValue, CustomLink $link, array $options = [])
    {
        return (string)$fieldValue;
    }

}
