<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Action\Erase;

use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Opengento\Gdpr\Api\Data\ActionContextInterface;
use Opengento\Gdpr\Api\Data\ActionResultInterface;
use Opengento\Gdpr\Api\Data\EraseEntityInterface;
use Opengento\Gdpr\Api\EraseEntityManagementInterface;
use Opengento\Gdpr\Api\EraseEntityRepositoryInterface;
use Opengento\Gdpr\Model\Action\AbstractAction;
use Opengento\Gdpr\Model\Action\ArgumentReader as ActionArgumentReader;
use Opengento\Gdpr\Model\Action\ResultBuilder;
use function array_reduce;

final class CancelAction extends AbstractAction
{
    /**
     * @var EraseEntityRepositoryInterface
     */
    private $eraseEntityRepository;

    /**
     * @var EraseEntityManagementInterface
     */
    private $eraseEntityManagement;

    public function __construct(
        ResultBuilder $resultBuilder,
        EraseEntityRepositoryInterface $eraseEntityRepository,
        EraseEntityManagementInterface $eraseEntityManagement
    ) {
        $this->eraseEntityRepository = $eraseEntityRepository;
        $this->eraseEntityManagement = $eraseEntityManagement;
        parent::__construct($resultBuilder);
    }

    public function execute(ActionContextInterface $actionContext): ActionResultInterface
    {
        $arguments = $this->getArguments($actionContext);

        return $this->createActionResult(
            [
                ArgumentReader::ERASE_ENTITY => $this->resolveEntity(...$arguments),
                'canceled' => $this->eraseEntityManagement->cancel(...$arguments)
            ]
        );
    }

    /**
     * @param int $entityId
     * @param string $entityType
     * @return EraseEntityInterface
     * @throws NoSuchEntityException
     */
    private function resolveEntity(int $entityId, string $entityType): EraseEntityInterface
    {
        return clone $this->eraseEntityRepository->getByEntity($entityId, $entityType);
    }

    private function getArguments(ActionContextInterface $actionContext): array
    {
        $entityId = ActionArgumentReader::getEntityId($actionContext);
        $entityType = ActionArgumentReader::getEntityType($actionContext);
        $errors = [];

        if ($entityId === null) {
            $errors[] = InputException::requiredField('entity_id');
        }
        if ($entityType === null) {
            $errors[] = InputException::requiredField('entity_type');
        }
        if (!empty($errors)) {
            throw array_reduce(
                $errors,
                static function (InputException $aggregated, InputException $input): InputException {
                    return $aggregated->addException($input);
                },
                new InputException()
            );
        }

        return [$entityId, $entityType];
    }
}
