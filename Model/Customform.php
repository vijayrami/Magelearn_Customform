<?php

declare(strict_types=1);

namespace Magelearn\Customform\Model;

use Magelearn\Customform\Api\Data\CustomformInterface;
use Magelearn\Customform\Api\Data\CustomformInterfaceFactory;
use Magelearn\Customform\Model\ResourceModel\Customform as CustomformResource;
use Magelearn\Customform\Model\ResourceModel\Customform\Collection as CustomformCollection;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;

class Customform extends AbstractModel
{
    protected $_eventPrefix = 'magelearn_customform';

    public function __construct(
        Context $context,
        Registry $registry,
        private readonly CustomformInterfaceFactory $customformDataFactory,
        private readonly DataObjectHelper $dataObjectHelper,
        CustomformResource $resource,
        CustomformCollection $resourceCollection,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    public function getDataModel(): CustomformInterface
    {
        $customformDataObject = $this->customformDataFactory->create();

        $this->dataObjectHelper->populateWithArray(
            $customformDataObject,
            $this->getData(),
            CustomformInterface::class
        );

        return $customformDataObject;
    }
}
