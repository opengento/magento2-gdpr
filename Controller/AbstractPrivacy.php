<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Controller;

use Magento\Customer\Controller\AbstractAccount;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;

/**
 * Controller AbstractPrivacy
 */
abstract class AbstractPrivacy extends AbstractAccount implements ActionInterface
{
    /**
     * Create a result forward to 404
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function forwardNoRoute(): ResultInterface
    {
        /** @var \Magento\Framework\Controller\Result\Forward $resultForward */
        $resultForward = $this->resultFactory->create(ResultFactory::TYPE_FORWARD);
        return $resultForward->forward('no_route');
    }
}
