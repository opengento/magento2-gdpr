<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Notifier;

use Magento\Framework\App\Area;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Store\Model\ScopeInterface;

/**
 * Class AbstractMailSender
 */
abstract class AbstractMailSender
{
    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $transportBuilder;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var string[]
     */
    private $configPaths;

    /**
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
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
     * Send an email to the recipient
     *
     * @param string $to
     * @param string|null $name
     * @param int|null $storeId
     * @param array $vars
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\MailException
     */
    protected function sendMail(string $to, ?string $name = null, ?int $storeId = null, array $vars = []): void
    {
        if ($this->isAvailable($storeId)) {
            $transport = $this->transportBuilder->setTemplateIdentifier($this->getTemplateIdentifier($storeId))
                ->setTemplateOptions(['area' => Area::AREA_FRONTEND, 'store' => $storeId])
                ->setTemplateVars($vars)
                ->setFromByScope($this->getFrom($storeId), $storeId)
                ->addTo($to, $name)
                ->getTransport();

            $transport->sendMessage();
        }
    }

    /**
     * Check if the sender is currently available
     *
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
     * Retrieve the email from
     *
     * @param int|null $storeId [optional] Retrieves the value by scope.
     * @return string
     */
    protected function getFrom(?int $storeId = null): string
    {
        return (string) $this->scopeConfig->getValue(
            $this->configPaths['from'],
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieve the email template identifier
     *
     * @param int|null $storeId [optional] Retrieves the value by scope.
     * @return string
     */
    protected function getTemplateIdentifier(?int $storeId = null): string
    {
        return (string) $this->scopeConfig->getValue(
            $this->configPaths['template_identifier'],
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
}
