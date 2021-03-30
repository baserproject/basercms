<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */

return [
    // Used for button elements in button().
    'button' => '<button{{attrs}}>{{text}}</button>',
    // Used for checkboxes in checkbox() and multiCheckbox().
    'checkbox' => '<input type="checkbox" name="{{name}}" value="{{value}}"{{attrs}}>',
    // Input group wrapper for checkboxes created via control().
    'checkboxFormGroup' => '{{label}}',
    // Wrapper container for checkboxes.
    'checkboxWrapper' => '<{{tag}} class="{{class}}">{{label}}</{{tag}}>',
    // Error message wrapper elements.
    'error' => '<div class="error-message">{{content}}</div>',
    // Container for error items.
    'errorList' => '<ul>{{content}}</ul>',
    // Error item wrapper.
    'errorItem' => '<li>{{text}}</li>',
    // File input used by file().
    'file' => '<input type="file" name="{{name}}"{{attrs}}>',
    // Fieldset element used by allControls().
    'fieldset' => '<fieldset{{attrs}}>{{content}}</fieldset>',
    // Open tag used by create().
    'formStart' => '<form{{attrs}}>',
    // Close tag used by end().
    'formEnd' => '</form>',
    // General grouping container for control(). Defines input/label ordering.
    'formGroup' => '{{label}}{{input}}',
    // Wrapper content used to hide other content.
    'hiddenBlock' => '<div style="display:none;">{{content}}</div>',
    // Generic input element.
    'input' => '<input type="{{type}}" name="{{name}}"{{attrs}}/>',
    // Submit input element.
    'inputSubmit' => '<input type="{{type}}"{{attrs}}/>',
    // Container element used by control().
    'inputContainer' => '<{{tag}} class="{{class}}">{{content}}</{{tag}}>',
    // Container element used by control().
    'multiCheckboxContainer' => '<{{groupTag}} class="{{groupClass}}">{{content}}</{{tag}}>',
    // Container element used by control() when a field has an error.
    'inputContainerError' => '<div class="input {{type}}{{required}} error">{{content}}{{error}}</div>',
    // Label element when inputs are not nested inside the label.
    'label' => '<label{{attrs}}>{{text}}</label>',
    // Label element used for radio and multi-checkbox inputs.
    'nestingLabel' => '{{hidden}}{{input}}<label{{attrs}} class="{{labelClass}}">{{text}}</label>',
    // Legends created by allControls()
    'legend' => '<legend>{{text}}</legend>',
    // Multi-Checkbox input set title element.
    'multicheckboxTitle' => '<legend>{{text}}</legend>',
    // Multi-Checkbox wrapping container.
    'multicheckboxWrapper' => '<fieldset{{attrs}}>{{content}}</fieldset>',
    // Option element used in select pickers.
    'option' => '<option value="{{value}}"{{attrs}}>{{text}}</option>',
    // Option group element used in select pickers.
    'optgroup' => '<optgroup label="{{label}}"{{attrs}}>{{content}}</optgroup>',
    // Select element,
    'select' => '<select name="{{name}}"{{attrs}}>{{content}}</select>',
    // Multi-select element,
    'selectMultiple' => '<select name="{{name}}[]" multiple="multiple"{{attrs}}>{{content}}</select>',
    // Radio input element,
    'radio' => '<input type="radio" name="{{name}}" value="{{value}}"{{attrs}}>',
    // Wrapping container for radio input/label,
    'radioWrapper' => '<span class="{{class}}">{{label}}</span>',
    'radioFormGroup' => '<span class="{{groupClass}}">{{input}}</span>',
    // Textarea input element,
    'textarea' => '<textarea name="{{name}}"{{attrs}}>{{value}}</textarea>',
    // Container for submit buttons.
    'submitContainer' => '<div class="submit">{{content}}</div>',
    // Confirm javascript template for postLink()
    'confirmJs' => '{{confirm}}',
    // selected class
    'selectedClass' => 'selected',
];
