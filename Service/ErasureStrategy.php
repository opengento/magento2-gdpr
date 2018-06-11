<?php
/**
 * Copyright © 2018 Uniwax, All rights reserved.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Service;

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
     * @param int $customerId
     * @return bool
     */
    public function execute(int $customerId): bool
    {
        foreach ($this->processorNames as $processorName) {
            $this->executeProcessorStrategy($processorName, $customerId);
        }

        return true;
    }

    /**
     * Execute the processor by strategy type
     *
     * @param string $processorName
     * @param int $customerId
     * @return bool
     */
    public function executeProcessorStrategy(string $processorName, int $customerId): bool
    {
        $strategyType = $this->config->getStrategySetting($processorName);

        switch ($strategyType) {
            case self::STRATEGY_ANONYMIZE:
                $result = $this->anonymizeManagement->executeProcessor($processorName, $customerId);
                break;
            case self::STRATEGY_DELETE:
                $result = $this->deleteManagement->executeProcessor($processorName, $customerId);
                break;
            default:
                throw new \InvalidArgumentException(sprintf('Unknown strategy type "%s".', $strategyType));
                break;
        }

        return $result;
    }
}
