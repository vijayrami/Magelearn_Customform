<?php

declare(strict_types=1);

namespace Magelearn\Customform\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Customform extends AbstractDb
{
    protected function _construct(): void
    {
        $this->_init('magelearn_customform', 'id');
    }
}
