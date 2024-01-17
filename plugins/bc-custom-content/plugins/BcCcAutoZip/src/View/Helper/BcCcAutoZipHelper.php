<?php
/**
 * CuCustomField : baserCMS Custom Field Text Plugin
 * Copyright (c) Catchup, Inc. <https://catchup.co.jp>
 *
 * @copyright        Copyright (c) Catchup, Inc.
 * @link             https://catchup.co.jp
 * @license          MIT LICENSE
 */

namespace BcCcAutoZip\View\Helper;

use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\View\Helper\BcAdminFormHelper;
use BcCustomContent\Model\Entity\CustomField;
use BcCustomContent\Model\Entity\CustomLink;
use Cake\View\Helper;

/**
 * Class BcCcAutoZipHelper
 *
 * @property BcAdminFormHelper $BcAdminForm
 */
class BcCcAutoZipHelper extends Helper
{

    /**
     * Helper
     * @var string[]
     */
    public array $helpers = [
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
     * @checked
     * @noTodo
     */
    public function control(CustomLink $link, array $options = []): string
    {
        $field = $link->custom_field;
        $options = array_merge([
            'type' => 'text',
            'size' => $field->size? : 10,
            'placeholder' => $field->placeholder,
            'style' => 'width:auto!important'
        ], $options);
        $pref = $link->custom_field->meta['BcCcAutoZip']['pref'];
        $address = $link->custom_field->meta['BcCcAutoZip']['address'];
        $options['onKeyUp'] = "AjaxZip3.zip2addr(this,'','{$pref}','{$address}')";
        return $this->BcBaser->js('vendor/ajaxzip3', false) .
            $this->BcAdminForm->control($link->name, $options);
    }

    /**
     * Search Control
     * @param string $fieldName
     * @param CustomField $field
     * @param array $options
     * @return string
     * @checked
     * @noTodo
     */
    public function searchControl(CustomLink $link, array $options = []): string
    {
        $options = array_merge([
            'type' => 'text',
            'size' => '',
            'max_length' => '',
            'placeholder' => ''
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
     * @checked
     * @noTodo
     */
    public function get($fieldValue, CustomLink $link, array $options = [])
    {
        return h($fieldValue);
    }

}
