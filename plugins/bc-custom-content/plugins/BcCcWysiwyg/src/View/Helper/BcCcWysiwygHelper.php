<?php
/**
 * CuCustomField : baserCMS Custom Field Text Plugin
 * Copyright (c) Catchup, Inc. <https://catchup.co.jp>
 *
 * @copyright        Copyright (c) Catchup, Inc.
 * @link             https://catchup.co.jp
 * @license          MIT LICENSE
 */

namespace BcCcWysiwyg\View\Helper;

use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Utility\BcSiteConfig;
use BaserCore\View\Helper\BcAdminFormHelper;
use BcCustomContent\Model\Entity\CustomField;
use BcCustomContent\Model\Entity\CustomLink;
use Cake\View\Helper;

/**
 * Class BcCcWysiwygHelper
 *
 * @property BcAdminFormHelper $BcAdminForm
 */
#[\AllowDynamicProperties]
class BcCcWysiwygHelper extends Helper
{

    /**
     * Helper
     * @var string[]
     */
    public array $helpers = [
        'BaserCore.BcAdminForm' => ['templates' => 'BaserCore.bc_form']
    ];

    /**
     * control
     *
     * @param string $fieldName
     * @param CustomField $field
     * @param array $options
     * @return string
     */
    public function control(CustomLink $link, array $options = []): string
    {
        $field = $link->custom_field;
        if(empty($field->meta['BcCcWysiwyg'])) return '';
        $options = array_merge([
			'editorHeight' => ($field->meta['BcCcWysiwyg']['height']) ?: '200px',
			'editorWidth' => ($field->meta['BcCcWysiwyg']['width']) ?: '100%',
			'editor' => 'BcCkeditor',
			'editorEnterBr' => BcSiteConfig::get('editor_enter_br'),
			'editorToolType' => $field->meta['BcCcWysiwyg']['editor_tool_type'],
		], $options);
		return $this->BcAdminForm->ckeditor($link->name, $options);
    }

    /**
     * プレビュー
     *
     * @param CustomLink $link
     * @return string
     */
    public function preview(CustomLink $link)
    {
        $link->name = 'wysiwyg';
        $this->BcAdminForm->unlockField($link->name);
        $options = [
            'value' => $link->custom_field->default_value,
        ];
        return $this->control($link, $options) . '<br>※ Wysiwyg エディタはリアルタイムでのプレビューは未対応です。保存してから確認してください。';
    }

    /**
     * Search Control
     *
     * @param string $fieldName
     * @param CustomField $field
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
     * @return mixed
     */
    public function get($fieldValue, CustomLink $link, array $options = [])
    {
        return $fieldValue;
    }

}
