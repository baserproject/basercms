<?php
/**
 * CuCustomField : baserCMS Custom Field Text Plugin
 * Copyright (c) Catchup, Inc. <https://catchup.co.jp>
 *
 * @copyright        Copyright (c) Catchup, Inc.
 * @link             https://catchup.co.jp
 * @license          MIT LICENSE
 */

namespace BcCcTel\View\Helper;

use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\View\Helper\BcAdminFormHelper;
use BcCustomContent\Model\Entity\CustomField;
use BcCustomContent\Model\Entity\CustomLink;
use Cake\View\Helper;

/**
 * Class BcCcTelHelper
 *
 * @property BcAdminFormHelper $BcAdminForm
 */
class BcCcTelHelper extends Helper
{

    /**
     * Helper
     * @var string[]
     */
    public $helpers = [
        'BaserCore.BcAdminForm' => ['templates' => 'BaserCore.bc_form'],
        'BaserCore.BcBaser'
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
        $options = array_merge([
            'type' => 'tel',
            'size' => $field->size,
            'maxlength' => $field->max_length,
            'placeholder' => $field->placeholder
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
            ':size' => 'entity.size',
            ':maxlength' => 'entity.max_length',
            ':placeholder' => 'entity.placeholder',
            ':value' => 'entity.default_value'
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
        $options = array_merge([
            'size' => '',
            'max_length' => '',
            'placeholder' => ''
        ], $options);
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
        $options = array_merge([
            'link' => true
        ], $options);
        if($options['link']) {
            return $this->BcBaser->getLink($fieldValue, 'tel:' . $fieldValue);
        } else {
            return h($fieldValue);
        }
    }

}
