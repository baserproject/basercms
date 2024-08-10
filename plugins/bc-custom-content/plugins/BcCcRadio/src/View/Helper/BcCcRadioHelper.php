<?php
/**
 * CuCustomField : baserCMS Custom Field Text Plugin
 * Copyright (c) Catchup, Inc. <https://catchup.co.jp>
 *
 * @copyright        Copyright (c) Catchup, Inc.
 * @link             https://catchup.co.jp
 * @license          MIT LICENSE
 */

namespace BcCcRadio\View\Helper;

use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\View\Helper\BcAdminFormHelper;
use BcCustomContent\Model\Entity\CustomField;
use BcCustomContent\Model\Entity\CustomLink;
use BcCustomContent\View\Helper\CustomContentArrayTrait;
use Cake\View\Helper;

/**
 * Class BcCcRadioHelper
 *
 * @property BcAdminFormHelper $BcAdminForm
 */
#[\AllowDynamicProperties]
class BcCcRadioHelper extends Helper
{

    /**
     * Trait
     */
    use CustomContentArrayTrait;

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
     */
    public function control(CustomLink $link, array $options = []): string
    {
        $options = array_merge([
            'type' => 'radio',
            'options' => $this->textToArray($link->custom_field->source),
        ], $options);
        return $this->BcAdminForm->control($link->name, $options);
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
            'empty' => __d('baser_core', '指定しない'),
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
			'novalue' => ''
		], $options);
		$selector = $this->textToArray($link->custom_field->source);
		return $this->arrayValue($fieldValue, $selector, $options['novalue']);
    }

    /**
     * プレビュー
     *
     * @param CustomLink $link
     * @return mixed
     */
    public function preview(CustomLink $link)
    {
        return $this->BcBaser->getElement('BcCcRadio.preview');
    }

}
