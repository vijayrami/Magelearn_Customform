<?php

declare(strict_types=1);

namespace Magelearn\Customform\Model;

use Magelearn\Customform\Api\CustomformRepositoryInterface;
use Magelearn\Customform\Api\Data\CustomformInterface;
use Magelearn\Customform\Api\Data\CustomformInterfaceFactory;
use Magelearn\Customform\Api\Data\CustomformSearchResultsInterface;
use Magelearn\Customform\Api\Data\CustomformSearchResultsInterfaceFactory;
use Magelearn\Customform\Model\ResourceModel\Customform as ResourceCustomform;
use Magelearn\Customform\Model\ResourceModel\Customform\CollectionFactory as CustomformCollectionFactory;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

class CustomformRepository implements CustomformRepositoryInterface
{
    public function __construct(
        private readonly ResourceCustomform $resource,
        private readonly CustomformFactory $customformFactory,
        private readonly CustomformInterfaceFactory $dataCustomformFactory,
        private readonly CustomformCollectionFactory $customformCollectionFactory,
        private readonly CustomformSearchResultsInterfaceFactory $searchResultsFactory,
        private readonly CollectionProcessorInterface $collectionProcessor,
        private readonly JoinProcessorInterface $extensionAttributesJoinProcessor,
        private readonly ExtensibleDataObjectConverter $extensibleDataObjectConverter
    ) {
    }

    public function save(CustomformInterface $customform): CustomformInterface
    {
        $customformData = $this->extensibleDataObjectConverter->toNestedArray(
            $customform,
            [],
            CustomformInterface::class
        );

        $customformModel = $this->customformFactory->create();
        $customformModel->setData($customformData);

        try {
            $this->resource->save($customformModel);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(
                __(
                    'Could not save the customform: %1',
                    $exception->getMessage()
                ),
                $exception
            );
        }

        return $customformModel->getDataModel();
    }

    public function get($customformId): CustomformInterface
    {
        $customform = $this->customformFactory->create();

        $this->resource->load($customform, $customformId);

        if (!$customform->getId()) {
            throw new NoSuchEntityException(
                __('Customform with id "%1" does not exist.', $customformId)
            );
        }

        return $customform->getDataModel();
    }

    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ): CustomformSearchResultsInterface {
        $collection = $this->customformCollectionFactory->create();

        $this->extensionAttributesJoinProcessor->process(
            $collection,
            CustomformInterface::class
        );

        $this->collectionProcessor->process($criteria, $collection);

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);

        $items = [];

        foreach ($collection as $model) {
            $items[] = $model->getDataModel();
        }

        $searchResults->setItems($items);
        $searchResults->setTotalCount($collection->getSize());

        return $searchResults;
    }

    public function delete(CustomformInterface $customform): bool
    {
        try {
            $customformModel = $this->customformFactory->create();

            $this->resource->load(
                $customformModel,
                $customform->getId()
            );

            $this->resource->delete($customformModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(
                __(
                    'Could not delete the customform: %1',
                    $exception->getMessage()
                ),
                $exception
            );
        }

        return true;
    }

    public function deleteById($customformId): bool
    {
        return $this->delete($this->get($customformId));
    }
}