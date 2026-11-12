<?php

declare(strict_types=1);

namespace Magelearn\Customform\Controller\Adminhtml;

use Magento\Backend\App\Action;

abstract class Customform extends Action
{
    public const ADMIN_RESOURCE = 'Magelearn_Customform::Customform';

    protected function initPage(\Magento\Backend\Model\View\Result\Page $resultPage)
        : \Magento\Backend\Model\View\Result\Page
    {
        $resultPage->setActiveMenu(self::ADMIN_RESOURCE)
            ->addBreadcrumb(__('Magelearn'), __('Magelearn'))
            ->addBreadcrumb(__('Customform'), __('Customform'));

        return $resultPage;
    }
}
