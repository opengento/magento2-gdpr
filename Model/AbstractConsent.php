<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model;

use Magento\Framework\Model\AbstractExtensibleModel;
use Opengento\Gdpr\Api\Data\ConsentInterface;

abstract class AbstractConsent extends AbstractExtensibleModel implements ConsentInterface
{
    public function getConsentId(): int
    {
        return (int) $this->_getData(self::ID);
    }

    public function setConsentId(int $consentId): ConsentInterface
    {
        return $this->setData(self::ID, $consentId);
    }

    public function getAgreementIdentifier(): string
    {
        return (string) $this->_getData(self::AGREEMENT_IDENTIFIER);
    }

    public function setAgreementIdentifier(string $agreementIdentifier): ConsentInterface
    {
        return $this->setData(self::AGREEMENT_IDENTIFIER, $agreementIdentifier);
    }

    public function isApproved(): bool
    {
        return (bool) $this->_getData(self::IS_APPROVED);
    }

    public function setIsApproved(bool $isApproved): ConsentInterface
    {
        return $this->setData(self::IS_APPROVED, $isApproved);
    }

    public function isOutDated(): bool
    {
        return (bool) $this->_getData(self::IS_OUTDATED);
    }

    public function setIsOutDated(bool $isOutDated): ConsentInterface
    {
        return $this->setData(self::IS_OUTDATED, $isOutDated);
    }

    public function getSubmittedAt(): string
    {
        return (string) $this->_getData(self::SUBMITTED_AT);
    }

    public function setSubmittedAt(string $submittedAt): ConsentInterface
    {
        return $this->setData(self::SUBMITTED_AT, $submittedAt);
    }
}
