<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\ResourceModel\ActionEntity\Validator;

use Magento\Framework\Phrase;
use Magento\Framework\Validator\AbstractValidator;
use Opengento\Gdpr\Api\Data\ActionEntityInterface;
use Opengento\Gdpr\Model\Config\Source\ActionStates;
use function array_column;
use function in_array;

final class StateValidator extends AbstractValidator
{
    /**
     * @var ActionStates
     */
    private $actionStates;

    public function __construct(
        ActionStates $actionStates
    ) {
        $this->actionStates = $actionStates;
    }

    /**
     * @param ActionEntityInterface $actionEntity
     * @return bool
     */
    public function isValid($actionEntity): bool
    {
        if (!in_array($actionEntity->getState(), array_column($this->actionStates->toOptionArray(), 'value'), true)) {
            $this->_addMessages([
                'state' => new Phrase('State "%1" does not exists.', [$actionEntity->getState()])
            ]);

            return false;
        }

        return true;
    }
}
