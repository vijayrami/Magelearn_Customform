<?php

declare(strict_types=1);

namespace Magelearn\Customform\Model\Customform;

use Magelearn\Customform\Model\ImageUploader;
use Magelearn\Customform\Model\ResourceModel\Customform\CollectionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;

class DataProvider extends AbstractDataProvider
{
    private ?array $loadedData = null;

    public function __construct(
        string $name,
        string $primaryFieldName,
        string $requestFieldName,
        CollectionFactory $collectionFactory,
        private readonly DataPersistorInterface $dataPersistor,
        private readonly ImageUploader $imageUploader,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $collectionFactory->create();
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    public function getData(): array
    {
        if ($this->loadedData !== null) {
            return $this->loadedData;
        }

        foreach ($this->collection->getItems() as $model) {
            $itemData = $model->getData();

            if ($model->getImage()) {
                $imageName = $this->imageUploader->getFileName((string) $model->getImage());
                $itemData['image'] = [
                    [
                        'name' => $imageName,
                        'url' => $this->imageUploader->getFileUrl($imageName),
                    ],
                ];
            }

            $this->loadedData[$model->getId()] = $itemData;
        }

        $data = $this->dataPersistor->get('mladmincustomform');

        if (!empty($data)) {
            $model = $this->collection->getNewEmptyItem();
            $model->setData($data);
            $this->loadedData[$model->getId()] = $model->getData();
            $this->dataPersistor->clear('mladmincustomform');
        }

        return $this->loadedData ?? [];
    }
}