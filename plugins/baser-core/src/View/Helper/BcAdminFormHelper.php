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

namespace BaserCore\View\Helper;

use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use BaserCore\Event\BcEventDispatcherTrait;

/**
 * Class BcAdminFormHelper
 * @package BaserCore\View\Helper
 */
class BcAdminFormHelper extends BcFormHelper
{
    /**
     * Trait
     */
    use BcEventDispatcherTrait;

    /**
     * control
     * @param string $name
     * @param array $options
     * @return string
     * @checked
     * @noTodo
     * @unitTest
     */
    public function control(string $name, array $options = []): string
    {
        if (!empty($options['type'])) {
            $options = array_replace_recursive([
                'label' => false,
                'legend' => false,
                'error' => false,
                'templateVars' => ['tag' => 'span', 'groupTag' => 'span']
            ], $options);
            $class = 'bca-hidden__input';
            $containerClass = 'bca-hidden';
            $labelClass = $groupContainerClass = $label = '';
            switch($options['type']) {
                case 'file':
                    $class = 'bca-file__input';
                    $containerClass = 'bca-file';
                    $options = array_replace_recursive([
                        'link' => ['class' => 'bca-file__link'],
                        'class' => 'bca-file__input',
                        'templateVars' => ['tag' => 'span', 'class' => 'bca-file'],
                        'deleteSpan' => ['class' => 'bca-file__delete'],
                        'deleteCheckbox' => ['class' => 'bca-file__delete-input', 'id' => true],
                        'deleteLabel' => ['class' => 'bca-file__delete-label'],
                        'figure' => ['class' => 'bca-file__figure'],
                        'img' => ['class' => 'bca-file__img'],
                        'figcaption' => ['class' => 'bca-file__figcaption']
                    ], $options);
                    break;
                case 'dateTimePicker':
                    $containerClass = 'bca-datetimepicker';
                    $options = array_replace_recursive([
                        'dateInput' => ['class' => 'bca-datetimepicker__date-input'],
                        'dateDiv' => ['tag' => 'span', 'class' => 'bca-datetimepicker__date'],
                        'dateLabel' => ['text' => '日付', 'class' => 'bca-datetimepicker__date-label'],
                        'timeInput' => ['class' => 'bca-datetimepicker__time-input'],
                        'timeDiv' => ['tag' => 'span', 'class' => 'bca-datetimepicker__time'],
                        'timeLabel' => ['text' => '時間', 'class' => 'bca-datetimepicker__time-label']
                    ], $options);
                    break;
                case 'text':
                case 'password':
                case 'datePicker':
                    $class = 'bca-textbox__input';
                    $containerClass = 'bca-textbox';
                    $labelClass = 'bca-textbox__label';
                    break;
                case 'textarea':
                    $class = 'bca-textarea__textarea';
                    $containerClass = 'bca-textarea';
                    break;
                case 'checkbox':
                    $options['templateVars']['labelClass'] = 'bca-checkbox__label';
                    $class = 'bca-checkbox__input';
                    $containerClass = 'bca-checkbox';
                    $labelClass = 'bca-checkbox__label';
                    break;
                case 'multiCheckbox':
                    $class = 'bca-checkbox__input';
                    $containerClass = 'bca-checkbox';
                    $labelClass = 'bca-checkbox__label';
                    $groupContainerClass = 'bca-checkbox-group';
                    break;
                case 'select':
                    $class = 'bca-select__select';
                    $containerClass = 'bca-select';
                    break;
                case 'radio':
                    $class = 'bca-radio__input';
                    $containerClass = 'bca-radio';
                    $labelClass = 'bca-radio__label';
                    $groupContainerClass = 'bca-radio-group';
                    break;
            }

            if (!isset($options['class'])) {
                $options['class'] = $class;
            }
            if (!isset($options['labelOptions'])) {
                if (!empty($options['label']) && $options['label'] !== true) {
                    $options['labelOptions'] = ['text' => $options['label'], 'class' => $labelClass];
                } else {
                    $options['labelOptions'] = ['class' => $labelClass];
                }
            }
            if ($containerClass) {
                $options['templateVars']['class'] = $containerClass;
            }
            if ($groupContainerClass) {
                $options['templateVars']['groupClass'] = $groupContainerClass;
            }

        }

        return parent::control($name, $options);

    }

    /**
     * postLink
     * CSSクラスに bca-submit-token を追加する
     * @param string $title
     * @param null $url
     * @param array $options
     * @return string
     * @checked
     * @noTodo
     * @unitTest
     */
    public function postLink(string $title, $url = null, array $options = []): string
    {
        $class = 'bca-submit-token';
        if(!empty($options['class'])) {
            $classes = explode(' ', $options['class']);
            if(!in_array($class, $classes)) {
                $classes[] = $class;
            }
            $options['class'] = implode(' ', $classes);
        } else {
                $options['class'] = $class;
        }
        return parent::postLink($title, $url, $options);
    }

}
