<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\ViewModel;

use Magento\Cms\Helper\Page as HelperPage;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Opengento\Gdpr\Model\Config;

final class UrlsDataProvider implements ArgumentInterface
{
    /**
     * Routes Path Data
     */
    public const ROUTE_PATH_SETTINGS = 'customer/privacy/settings';
    public const ROUTE_PATH_ERASE = 'customer/privacy/erase';
    public const ROUTE_PATH_ERASE_POST = 'customer/privacy/erasepost';
    public const ROUTE_PATH_UNDO_ERASE = 'customer/privacy/undoerase';
    public const ROUTE_PATH_EXPORT = 'customer/privacy/export';
    public const ROUTE_PATH_DOWNLOAD = 'customer/privacy/download';

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @var HelperPage
     */
    private $helperPage;

    /**
     * @var Config
     */
    private $config;

    public function __construct(
        UrlInterface $urlBuilder,
        HelperPage $helperPage,
        Config $config
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->helperPage = $helperPage;
        $this->config = $config;
    }

    public function getInformationPageUrl(): string
    {
        return (string) $this->helperPage->getPageUrl($this->config->getPrivacyInformationPageId());
    }

    public function getSettingsPageUrl(): string
    {
        return $this->urlBuilder->getUrl(self::ROUTE_PATH_SETTINGS);
    }

    public function getErasePageUrl(): string
    {
        return $this->urlBuilder->getUrl(self::ROUTE_PATH_ERASE);
    }

    public function getEraseActionUrl(): string
    {
        return $this->urlBuilder->getUrl(self::ROUTE_PATH_ERASE_POST);
    }

    public function getUndoEraseActionUrl(): string
    {
        return $this->urlBuilder->getUrl(self::ROUTE_PATH_UNDO_ERASE);
    }

    public function getExportActionUrl(): string
    {
        return $this->urlBuilder->getUrl(self::ROUTE_PATH_EXPORT);
    }

    public function getDownloadActionUrl(): string
    {
        return $this->urlBuilder->getUrl(self::ROUTE_PATH_DOWNLOAD);
    }
}
