<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Controller\Guest;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;
use Magento\Framework\Registry;
use Magento\Sales\Controller\AbstractController\OrderLoaderInterface;
use Opengento\Gdpr\Api\EraseGuestInterface;
use Opengento\Gdpr\Controller\AbstractGuest;
use Opengento\Gdpr\Model\Config;

/**
 * Class Erase
 */
class Erase extends AbstractGuest
{
    private $eraseGuest;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Opengento\Gdpr\Model\Config $config
     * @param \Magento\Sales\Controller\AbstractController\OrderLoaderInterface $orderLoader
     * @param \Opengento\Gdpr\Api\EraseGuestInterface $eraseGuest
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        Context $context,
        Config $config,
        OrderLoaderInterface $orderLoader,
        EraseGuestInterface $eraseGuest,
        Registry $registry
    ) {
        $this->eraseGuest = $eraseGuest;
        parent::__construct($context, $config, $orderLoader, $registry);
    }

    /**
     * @inheritdoc
     */
    protected function isAllowed(): bool
    {
        return parent::isAllowed() && $this->config->isErasureEnabled();
    }

    /**
     * @inheritdoc
     */
    protected function executeAction()
    {
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setRefererOrBaseUrl();

        try {
            //todo refactor with eraseCustomer accepting guest?
            //todo check erase is possible for the current guest order
            $this->eraseGuest->erase($this->retrieveOrder());
            $this->messageManager->addWarningMessage(new Phrase('Your personal data is being removed.'));
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, new Phrase('Something went wrong, please try again later!'));
        }

        return $resultRedirect;
    }
}
