<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Action\Erase;

use Magento\Framework\Exception\InputException;
use Magento\Framework\ObjectManagerInterface;
use Opengento\Gdpr\Api\Data\ActionContextInterface;
use Opengento\Gdpr\Api\Data\ActionResultInterface;
use Opengento\Gdpr\Model\Action\AbstractAction;
use Opengento\Gdpr\Model\Action\ArgumentReader as ActionArgumentReader;
use Opengento\Gdpr\Model\Action\ResultBuilder;
use Opengento\Gdpr\Model\Erase\NotifierInterface;

final class NotifierActionBundle extends AbstractAction
{
    /**
     * @var string[]
     */
    private $notifiers;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    public function __construct(
        ResultBuilder $resultBuilder,
        array $notifiers,
        ObjectManagerInterface $objectManager
    ) {
        $this->notifiers = $notifiers;
        $this->objectManager = $objectManager;
        parent::__construct($resultBuilder);
    }

    public function execute(ActionContextInterface $actionContext): ActionResultInterface
    {
        $this->resolveNotifier($actionContext)->notify(ArgumentReader::getEntity($actionContext));

        return $this->createActionResult(['is_notify' => true]);
    }

    /**
     * @param ActionContextInterface $actionContext
     * @return NotifierInterface
     * @throws InputException
     */
    private function resolveNotifier(ActionContextInterface $actionContext): NotifierInterface
    {
        $entityType = ActionArgumentReader::getEntityType($actionContext);

        if (!isset($this->notifiers[$entityType])) {
            throw InputException::invalidFieldValue('entity_type', $entityType);
        }

        return $this->objectManager->get($this->notifiers[$entityType]);
    }
}
