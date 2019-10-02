<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Ui\Component\Listing\Column;

use Magento\Framework\Phrase;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

final class Actions extends Column
{
    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    public function prepareDataSource(array $dataSource): array
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                if (isset($item['action_id'])) {
                    $item[$this->getData('name')]['cancel'] = [
                        'href' => $this->urlBuilder->getUrl(
                            'gdpr/*/cancel',
                            ['id' => $item['action_id']]
                        ),
                        'label' => new Phrase('Cancel'),
                        'confirm' => [
                            'title' => new Phrase('Cancel Action'),
                            'message' => new Phrase('Are you sure you want to cancel the selected action?'),
                            '__disableTmpl' => true,
                        ],
                        'post' => true,
                    ];
                }
            }
        }

        return $dataSource;
    }
}
