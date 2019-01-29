<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Controller\Privacy;

use Magento\Framework\App\ActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Opengento\Gdpr\Controller\AbstractPrivacy;

/**
 * Action Index Settings
 */
class Settings extends AbstractPrivacy implements ActionInterface
{
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        return $this->resultFactory->create(ResultFactory::TYPE_PAGE);
    }
}
