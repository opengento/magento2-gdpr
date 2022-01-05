<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\ResourceModel\ActionEntity;

use Magento\Framework\Validator\AbstractValidator;
use Magento\Framework\Validator\ValidatorInterface;

final class Validator extends AbstractValidator
{
    /**
     * @var ValidatorInterface[]
     */
    private array $validators;

    public function __construct(
        array $validators
    ) {
        $this->validators = $validators;
    }

    public function isValid($value): bool
    {
        $this->_clearMessages();

        foreach ($this->validators as $validator) {
            if (!$validator->isValid($value)) {
                $this->_addMessages($validator->getMessages());
            }
        }

        return !$this->hasMessages();
    }
}
