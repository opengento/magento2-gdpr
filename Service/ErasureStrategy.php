<?php
/**
 * Copyright © 2018 Uniwax, All rights reserved.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Service;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;
use Opengento\Gdpr\Model\Config;

/**
 * Class PrivacyStrategy
 * @api
 */
class ErasureStrategy
{
    /**#@+
     * Strategy Constant Values
     */
    const STRATEGY_ANONYMIZE = 'anonymize';
    const STRATEGY_DELETE = 'delete';
    /**#@-*/

    /**
     * @var \Opengento\Gdpr\Model\Config
     */
    private $config;

    /**
     * @var \Opengento\Gdpr\Service\AnonymizeManagement
     */
    private $anonymizeManagement;

    /**
     * @var \Opengento\Gdpr\Service\DeleteManagement
     */
    private $deleteManagement;

    /**
     * @var string[]
     */
    private $processorNames;

    /**
     * @param \Opengento\Gdpr\Model\Config $config
     * @param \Opengento\Gdpr\Service\AnonymizeManagement $anonymizeManagement
     * @param \Opengento\Gdpr\Service\DeleteManagement $deleteManagement
     * @param string[] $processorNames
     */
    public function __construct(
        Config $config,
        AnonymizeManagement $anonymizeManagement,
        DeleteManagement $deleteManagement,
        array $processorNames = []
    ) {
        $this->config = $config;
        $this->anonymizeManagement = $anonymizeManagement;
        $this->deleteManagement = $deleteManagement;
        $this->processorNames = $processorNames;
    }

    /**
     * Execute the processors by strategy type
     *
     * @param string $customerEmail
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(string $customerEmail): bool
    {
        foreach ($this->processorNames as $processorName) {
            $this->executeProcessorStrategy($processorName, $customerEmail);
        }

        return true;
    }

    /**
     * Execute the processor by strategy type
     *
     * @param string $processorName
     * @param string $customerEmail
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function executeProcessorStrategy(string $processorName, string $customerEmail): bool
    {
        $strategyType = $this->config->getStrategySetting($processorName);

        switch ($strategyType) {
            case self::STRATEGY_ANONYMIZE:
                $result = $this->anonymizeManagement->executeProcessor($processorName, $customerEmail);
                break;
            case self::STRATEGY_DELETE:
                $result = $this->deleteManagement->executeProcessor($processorName, $customerEmail);
                break;
            default:
                throw new LocalizedException(new Phrase('Unknown strategy type "%1".', [$strategyType]));
                break;
        }

        return $result;
    }
}
