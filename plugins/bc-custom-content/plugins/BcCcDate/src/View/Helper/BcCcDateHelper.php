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

namespace BcCcDate\View\Helper;

use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\View\Helper\BcAdminFormHelper;
use BaserCore\View\Helper\BcTimeHelper;
use BcCustomContent\Model\Entity\CustomField;
use BcCustomContent\Model\Entity\CustomLink;
use Cake\View\Helper;

/**
 * Class BcCcDateHelper
 *
 * @property BcAdminFormHelper $BcAdminForm
 * @property BcTimeHelper $BcTime
 */
#[\AllowDynamicProperties]
class BcCcDateHelper extends Helper
{

    /**
     * Helper
     * @var string[]
     */
    public array $helpers = [
        'BaserCore.BcAdminForm' => ['templates' => 'BaserCore.bc_form'],
        'BaserCore.BcTime'
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
            'type' => 'datePicker',
            'size' => $field->size
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
            'v-model' => "entity.default_value"
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
            'format' => 'yyyy/MM/dd'
        ], $options);
        return $this->BcTime->format($fieldValue, $options['format']);
    }

}
