<?php

declare(strict_types=1);

namespace Magelearn\Customform\Model\ResourceModel\Customform;

use Magelearn\Customform\Model\Customform as CustomformModel;
use Magelearn\Customform\Model\ResourceModel\Customform as CustomformResource;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'id';

    protected function _construct(): void
    {
        $this->_init(CustomformModel::class, CustomformResource::class);
        $this->_map['fields']['id'] = 'main_table.id';
    }
}
