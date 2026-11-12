<?php

declare(strict_types=1);

namespace Magelearn\Customform\Controller\Adminhtml\Customform;

use Magelearn\Customform\Api\CustomformRepositoryInterface;
use Magelearn\Customform\Controller\Adminhtml\Customform as CustomformController;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;

class Delete extends CustomformController
{
    public function __construct(
        Context $context,
        private readonly CustomformRepositoryInterface $customformRepository
    ) {
        parent::__construct($context);
    }

    public function execute(): \Magento\Framework\Controller\ResultInterface
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $id = (int) $this->getRequest()->getParam('id');

        if (!$id) {
            $this->messageManager->addErrorMessage(__("We can't find a Customform to delete."));
            return $resultRedirect->setPath('*/*/');
        }

        try {
            $this->customformRepository->deleteById($id);
            $this->messageManager->addSuccessMessage(__('You deleted the Customform.'));
            return $resultRedirect->setPath('*/*/');
        } catch (LocalizedException $exception) {
            $this->messageManager->addErrorMessage($exception->getMessage());
        } catch (\Throwable $exception) {
            $this->messageManager->addExceptionMessage(
                $exception,
                __('Something went wrong while deleting the Customform.')
            );
        }

        return $resultRedirect->setPath('*/*/edit', ['id' => $id]);
    }
}
