<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Notifier;

use Magento\Framework\App\Area;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\MailException;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Store\Model\ScopeInterface;

use function explode;

abstract class AbstractMailSender
{
    protected TransportBuilder $transportBuilder;

    private ScopeConfigInterface $scopeConfig;

    /**
     * @var string[]
     */
    private array $configPaths;

    /**
     * @param TransportBuilder $transportBuilder
     * @param ScopeConfigInterface $scopeConfig
     * @param string[] $configPaths
     */
    public function __construct(
        TransportBuilder $transportBuilder,
        ScopeConfigInterface $scopeConfig,
        array $configPaths
    ) {
        $this->transportBuilder = $transportBuilder;
        $this->scopeConfig = $scopeConfig;
        $this->configPaths = $configPaths;
    }

    /**
     * @param string $sendTo
     * @param string|null $name [optional] Specify the to name.
     * @param int|null $storeId [optional Current store ID is used by default.
     * @param array $vars
     * @throws LocalizedException
     * @throws MailException
     */
    protected function sendMail(string $sendTo, ?string $name = null, ?int $storeId = null, array $vars = []): void
    {
        if ($this->isAvailable($storeId)) {
            $copyTo = $this->getCopyTo($storeId);

            if ($copyTo) {
                $copyMethod = $this->getCopyMethod($storeId);
                if ($copyMethod === 'copy') {
                    foreach ($copyTo as $email) {
                        $this->prepareMail($email, $name, $storeId, $vars);
                        $this->transportBuilder->getTransport()->sendMessage();
                    }
                } elseif ($copyMethod === 'bcc') {
                    foreach ($copyTo as $email) {
                        $this->transportBuilder->addBcc($email);
                    }
                }
            }

            $this->prepareMail($sendTo, $name, $storeId, $vars);
            $transport = $this->transportBuilder->getTransport();

            $transport->sendMessage();
        }
    }

    /**
     * @param string $sendTo
     * @param string|null $name [optional] Specify the to name.
     * @param int|null $storeId [optional Current store ID is used by default.
     * @param array $vars
     * @throws MailException
     */
    protected function prepareMail(string $sendTo, ?string $name = null, ?int $storeId = null, array $vars = []): void
    {
        $this->transportBuilder->setTemplateIdentifier($this->getTemplateIdentifier($storeId))
            ->setTemplateOptions(['area' => Area::AREA_FRONTEND, 'store' => $storeId])
            ->setTemplateVars($vars)
            ->setFromByScope($this->getFrom($storeId), $storeId)
            ->addTo($sendTo, $name);
    }

    /**
     * @param int|null $storeId [optional] Retrieves the value by scope.
     * @return bool
     */
    protected function isAvailable(?int $storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            $this->configPaths['is_available'],
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param int|null $storeId [optional] Retrieves the value by scope.
     * @return string
     */
    protected function getFrom(?int $storeId = null): string
    {
        return (string)$this->scopeConfig->getValue(
            $this->configPaths['from'],
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param int|null $storeId [optional] Retrieves the value by scope.
     * @return array
     */
    protected function getCopyTo(?int $storeId = null): array
    {
        return explode(
            ',',
            $this->scopeConfig->getValue(
                $this->configPaths['copy_to'],
                ScopeInterface::SCOPE_STORE,
                $storeId
            ) ?? ''
        );
    }

    /**
     * @param int|null $storeId [optional] Retrieves the value by scope.
     * @return string
     */
    protected function getCopyMethod(?int $storeId = null): string
    {
        return (string)$this->scopeConfig->getValue(
            $this->configPaths['copy_method'],
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param int|null $storeId [optional] Retrieves the value by scope.
     * @return string
     */
    protected function getTemplateIdentifier(?int $storeId = null): string
    {
        return (string)$this->scopeConfig->getValue(
            $this->configPaths['template_identifier'],
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
}
