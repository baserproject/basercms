<?php
/**
 * CuCustomField : baserCMS Custom Field Text Plugin
 * Copyright (c) Catchup, Inc. <https://catchup.co.jp>
 *
 * @copyright        Copyright (c) Catchup, Inc.
 * @link             https://catchup.co.jp
 * @package          CuCfText.View.Helper
 * @license          MIT LICENSE
 */

namespace BcCcCheckbox\View\Helper;

use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\View\Helper\BcAdminFormHelper;
use BcCustomContent\Model\Entity\CustomField;
use BcCustomContent\Model\Entity\CustomLink;
use BcCustomContent\View\Helper\CustomContentArrayTrait;
use Cake\View\Helper;

/**
 * Class BcCcCheckboxHelper
 *
 * @property BcAdminFormHelper $BcAdminForm
 */
class BcCcCheckboxHelper extends Helper
{

    /**
     * Trait
     */
    use CustomContentArrayTrait;

    /**
     * Helper
     * @var string[]
     */
    public $helpers = [
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
        $label = '';
        if(!empty($link->custom_field->meta['BcCcCheckbox']['label'])) {
            $label = $link->custom_field->meta['BcCcCheckbox']['label'];
        }
        $options = array_merge([
            'type' => 'checkbox',
            'label' => $label,
        ], $options);
        return $this->BcAdminForm->control($link->name, $options);
    }

    /**
     * プレビュー
     *
     * @param CustomLink $link
     * @return string
     */
    public function preview(CustomLink $link)
    {
        $options = [
            'v-model' => 'entity.default_value',
            'label' => '{{checkboxLabel}}',
        ];
        return $this->control($link, $options);
    }

    /**
     * Search Control
     * @param string $fieldName
     * @param CustomField $field
     * @param array $options
     * @return string
     */
    public function searchControl(CustomLink $link, array $options = []): string
    {
        return $this->control($link, $options);
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
		return h($fieldValue);
    }

}
