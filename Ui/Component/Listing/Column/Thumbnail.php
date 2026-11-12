<?php

declare(strict_types=1);

namespace Magelearn\Customform\Ui\Component\Listing\Column;

use Magelearn\Customform\Model\ImageUploader;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

class Thumbnail extends Column
{
    private const PLACEHOLDER = 'placeholder/placeholder.jpg';

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        private readonly ImageUploader $imageUploader,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    public function prepareDataSource(array $dataSource): array
    {
        if (!isset($dataSource['data']['items'])) {
            return $dataSource;
        }

        $fieldName = (string) $this->getData('name');

        foreach ($dataSource['data']['items'] as &$item) {
            $image = (string) ($item[$fieldName] ?? '');
            $imageUrl = $image !== ''
                ? $this->imageUploader->getFileUrl($image)
                : $this->imageUploader->getFileUrl(self::PLACEHOLDER);

            $item[$fieldName . '_src'] = $imageUrl;
            $item[$fieldName . '_orig_src'] = $imageUrl;
            $item[$fieldName . '_alt'] = trim(
                (string) ($item['first_name'] ?? '') . ' ' . (string) ($item['last_name'] ?? '')
            );
        }
        unset($item);

        return $dataSource;
    }
}