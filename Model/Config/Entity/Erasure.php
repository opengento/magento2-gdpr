<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Config\Entity;

use DateTimeImmutable;
use Exception;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

use function explode;

class Erasure
{
    private const CONFIG_PATH_ERASURE_MAX_AGE = 'gdpr/erasure/entity_max_age';
    private const CONFIG_PATH_ERASURE_ALLOWED_STATES = 'gdpr/erasure/allowed_states';
    private const CONFIG_PATH_ERASURE_DELAY = 'gdpr/erasure/delay';

    public function __construct(
        private ScopeConfigInterface $scopeConfig
    ) {}

    public function getEntityMaxAge(int|string|null $website = null): int
    {
        return (int)$this->scopeConfig->getValue(
            self::CONFIG_PATH_ERASURE_MAX_AGE,
            ScopeInterface::SCOPE_WEBSITE,
            $website
        );
    }

    /**
     * @throws Exception
     */
    public function getEntityExpireDate(int|string|null $website = null): DateTimeImmutable
    {
        return new DateTimeImmutable('-' . $this->getEntityMaxAge($website) . 'days');
    }

    /**
     * @return string[]
     */
    public function getAllowedStatesToErase(int|string|null $website = null): array
    {
        return explode(',', (string)$this->scopeConfig->getValue(
            self::CONFIG_PATH_ERASURE_ALLOWED_STATES,
            ScopeInterface::SCOPE_WEBSITE,
            $website
        ));
    }

    /**
     * @return int
     */
    public function getDelay(): int
    {
        return (int)$this->scopeConfig->getValue(self::CONFIG_PATH_ERASURE_DELAY, ScopeInterface::SCOPE_WEBSITE);
    }
}
