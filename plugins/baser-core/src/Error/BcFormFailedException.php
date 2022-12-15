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

namespace BaserCore\Error;

use Cake\Core\Exception\CakeException;
use Cake\Utility\Hash;
use Cake\Validation\ValidatorAwareInterface;
use Throwable;

/**
 * BcFormFailedException
 */
class BcFormFailedException extends CakeException {

    /**
     * Form
     *
     * @var ValidatorAwareInterface
     */
    protected $_form;

    /**
     * @inheritDoc
     */
    protected $_messageTemplate = 'Form %s failure.';

    /**
     * Constructor.
     *
     * @param \Cake\Datasource\EntityInterface $entity
     * @param array<string>|string $message
     * @param int|null $code
     * @param \Throwable|null $previous
     */
    public function __construct(ValidatorAwareInterface $form, $message, ?int $code = null, ?Throwable $previous = null)
    {
        $this->_entity = $form;
        if (is_array($message)) {
            $errors = [];
            foreach (Hash::flatten($form->getErrors()) as $field => $error) {
                $errors[] = $field . ': "' . $error . '"';
            }
            if ($errors) {
                $message[] = implode(', ', $errors);
                $this->_messageTemplate = 'Form %s failure. Found the following errors (%s).';
            }
        }
        parent::__construct($message, $code, $previous);
    }

    /**
     * フォームを取得する
     *
     * @return ValidatorAwareInterface
     */
    public function getForm(): ValidatorAwareInterface
    {
        return $this->_form;
    }

}
