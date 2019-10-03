<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Controller\Adminhtml\Action;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\EntityManager\HydratorPool;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;
use Opengento\Gdpr\Api\ActionEntityRepositoryInterface;
use Opengento\Gdpr\Api\Data\ActionEntityInterface;
use Psr\Log\LoggerInterface;
use function array_fill_keys;
use function array_intersect_key;
use function is_array;

class InlineEdit extends Action implements HttpPostActionInterface
{
    public const ADMIN_RESOURCE = 'Opengento_Gdpr::gdpr_actions_edit';

    /**
     * @var ActionEntityRepositoryInterface
     */
    private $actionEntityRepository;

    /**
     * @var HydratorPool
     */
    private $hydratorPool;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var string[]
     */
    private $allowedAttributes;

    public function __construct(
        Context $context,
        ActionEntityRepositoryInterface $actionEntityRepository,
        HydratorPool $hydratorPool,
        LoggerInterface $logger,
        array $allowedAttributes
    ) {
        $this->actionEntityRepository = $actionEntityRepository;
        $this->hydratorPool = $hydratorPool;
        $this->logger = $logger;
        $this->allowedAttributes = $allowedAttributes;
        parent::__construct($context);
    }

    public function execute()
    {
        /** @var Json $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $error = false;
        $messages = [];
        $postItems = $this->getRequest()->getParam('items', []);

        if (!$postItems || !is_array($postItems) || !$this->getRequest()->getParam('isAjax')) {
            return $resultJson->setData(['messages' => [new Phrase('Please correct the data sent.')], 'error' => true]);
        }

        foreach ($postItems as $actionId => $item) {
            try {
                $this->edit($actionId, $item);
            } catch (LocalizedException $e) {
                $messages[] = new Phrase('Action with ID "%1": %2', [$actionId, $e->getMessage()]);
                $error = true;
            } catch (Exception $e) {
                $this->logger->error($e->getMessage(), $e->getTrace());
                $messages[] = new Phrase(
                    'Action with ID "%1": %2',
                    [$actionId, new Phrase('An error occurred on the server.')]
                );
                $error = true;
            }
        }

        return $resultJson->setData(compact('messages', 'error'));
    }

    /**
     * @param int $actionId
     * @param array $data
     * @return void
     * @throws LocalizedException
     */
    private function edit(int $actionId, array $data): void
    {
        $hydrator = $this->hydratorPool->getHydrator(ActionEntityInterface::class);



        /** @var ActionEntityInterface $actionEntity */
        $actionEntity = $hydrator->hydrate(
            $this->actionEntityRepository->getById($actionId),
            array_intersect_key($data, array_fill_keys($this->allowedAttributes, null))
        );

        //todo disallow edit parameters and scheduled_at when state is not set to "pending"

        $this->actionEntityRepository->save($actionEntity);
    }
}
