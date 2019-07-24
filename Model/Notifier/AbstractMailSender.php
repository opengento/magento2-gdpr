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

            $this->prepareMail($to, $name, $storeId, $vars);
            $transport = $this->transportBuilder->getTransport();

            $transport->sendMessage();
        }
    }

    /**
     * Prepare the mail to send
     *
     * @param string $to
     * @param string|null $name
     * @param int|null $storeId
     * @param array $vars
     * @throws \Magento\Framework\Exception\MailException
     */
    protected function prepareMail(string $to, ?string $name = null, ?int $storeId = null, array $vars = []): void
    {
        $this->transportBuilder->setTemplateIdentifier($this->getTemplateIdentifier($storeId))
            ->setTemplateOptions(['area' => Area::AREA_FRONTEND, 'store' => $storeId])
            ->setTemplateVars($vars)
            ->setFromByScope($this->getFrom($storeId), $storeId)
            ->addTo($to, $name);
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
     * Retrieve the email copy to
     *
     * @param int|null $storeId [optional] Retrieves the value by scope.
     * @return array
     */
    protected function getCopyTo(?int $storeId = null): array
    {
        return \explode(
            ',',
            $this->scopeConfig->getValue(
                $this->configPaths['copy_to'],
                ScopeInterface::SCOPE_STORE,
                $storeId
            ) ?? ''
        );
    }

    /**
     * Retrieve the email copy method
     *
     * @param int|null $storeId [optional] Retrieves the value by scope.
     * @return string
     */
    protected function getCopyMethod(?int $storeId = null): string
    {
        return (string) $this->scopeConfig->getValue(
            $this->configPaths['copy_method'],
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
