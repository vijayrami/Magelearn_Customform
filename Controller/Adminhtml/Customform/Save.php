<?php

declare(strict_types=1);

namespace Magelearn\Customform\Controller\Adminhtml\Customform;

use Magelearn\Customform\Api\CustomformRepositoryInterface;
use Magelearn\Customform\Controller\Adminhtml\Customform as CustomformController;
use Magelearn\Customform\Model\CustomformFactory;
use Magelearn\Customform\Model\ImageUploader;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\LocalizedException;

class Save extends CustomformController
{
    public function __construct(
        Context $context,
        private readonly DataPersistorInterface $dataPersistor,
        private readonly CustomformRepositoryInterface $customformRepository,
        private readonly CustomformFactory $customformFactory,
        private readonly ImageUploader $imageUploader
    ) {
        parent::__construct($context);
    }

    public function execute(): \Magento\Framework\Controller\ResultInterface
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();

        if (!$data) {
            return $resultRedirect->setPath('*/*/');
        }

        $id = (int) $this->getRequest()->getParam('id');

        try {
            $model = $this->customformFactory->create();

            if ($id) {
                $model->load($id);

                if (!$model->getId()) {
                    $this->messageManager->addErrorMessage(__('This Customform no longer exists.'));
                    return $resultRedirect->setPath('*/*/');
                }
            }

            if (isset($data['image'][0]['name'])) {
                $imageName = $data['image'][0]['name'];

                if (isset($data['image'][0]['tmp_name'])) {
                    $this->imageUploader->moveFileFromTmp($imageName);
                }

                $data['image'] = $imageName;
            } else {
                $data['image'] = $model->getImage();
            }

            $model->setData($data);
            $savedData = $this->customformRepository->save($model->getDataModel());

            $this->messageManager->addSuccessMessage(__('You saved the Customform.'));
            $this->dataPersistor->clear('mladmincustomform');

            $savedId = (int) $savedData->getId();

            if ($this->getRequest()->getParam('back')) {
                return $resultRedirect->setPath('*/*/edit', ['id' => $savedId]);
            }

            return $resultRedirect->setPath('*/*/');
        } catch (LocalizedException $exception) {
            $this->messageManager->addErrorMessage($exception->getMessage());
        } catch (\Throwable $exception) {
            $this->messageManager->addExceptionMessage(
                $exception,
                __('Something went wrong while saving the Customform.')
            );
        }

        $this->dataPersistor->set('mladmincustomform', $data);

        return $resultRedirect->setPath('*/*/edit', ['id' => $id]);
    }
}
