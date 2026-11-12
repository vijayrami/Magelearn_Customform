<?php

declare(strict_types=1);

namespace Magelearn\Customform\Controller\Adminhtml\Customform;

use Magelearn\Customform\Api\CustomformRepositoryInterface;
use Magelearn\Customform\Controller\Adminhtml\Customform as CustomformController;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Result\PageFactory;

class Edit extends CustomformController
{
    public function __construct(
        Context $context,
        private readonly PageFactory $resultPageFactory,
        private readonly CustomformRepositoryInterface $customformRepository
    ) {
        parent::__construct($context);
    }

    public function execute(): \Magento\Framework\Controller\ResultInterface
    {
        $id = (int) $this->getRequest()->getParam('id');

        if ($id) {
            try {
                $this->customformRepository->get($id);
            } catch (NoSuchEntityException) {
                $this->messageManager->addErrorMessage(__('This Customform no longer exists.'));
                return $this->resultRedirectFactory->create()->setPath('*/*/');
            }
        }

        $resultPage = $this->resultPageFactory->create();
        $this->initPage($resultPage)->addBreadcrumb(
            $id ? __('Edit Customform') : __('New Customform'),
            $id ? __('Edit Customform') : __('New Customform')
        );

        $resultPage->getConfig()->getTitle()->prepend(__('Customforms'));
        $resultPage->getConfig()->getTitle()->prepend(
            $id ? __('Edit Customform %1', $id) : __('New Customform')
        );

        return $resultPage;
    }
}
