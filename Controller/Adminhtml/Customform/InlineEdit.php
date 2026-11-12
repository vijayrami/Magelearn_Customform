<?php

declare(strict_types=1);

namespace Magelearn\Customform\Controller\Adminhtml\Customform;

use Magelearn\Customform\Model\CustomformFactory;
use Magento\Backend\App\Action;
use Magento\Framework\Controller\Result\JsonFactory;

class InlineEdit extends Action
{
    public function __construct(
        Action\Context $context,
        private readonly JsonFactory $jsonFactory,
        private readonly CustomformFactory $customformFactory
    ) {
        parent::__construct($context);
    }

    public function execute(): \Magento\Framework\Controller\ResultInterface
    {
        $resultJson = $this->jsonFactory->create();
        $messages = [];
        $error = false;

        if (!$this->getRequest()->getParam('isAjax')) {
            return $resultJson->setData([
                'messages' => [__('Invalid request.')],
                'error' => true,
            ]);
        }

        $postItems = $this->getRequest()->getParam('items', []);

        if (!$postItems) {
            return $resultJson->setData([
                'messages' => [__('Please correct the data sent.')],
                'error' => true,
            ]);
        }

        foreach ($postItems as $modelId => $itemData) {
            try {
                $model = $this->customformFactory->create()->load((int) $modelId);

                if (!$model->getId()) {
                    throw new \Magento\Framework\Exception\LocalizedException(
                        __('Customform with ID %1 does not exist.', $modelId)
                    );
                }

                $model->addData($itemData);
                $model->save();
            } catch (\Throwable $exception) {
                $messages[] = __(
                    '[Customform ID: %1] %2',
                    $modelId,
                    $exception->getMessage()
                );
                $error = true;
            }
        }

        return $resultJson->setData([
            'messages' => $messages,
            'error' => $error,
        ]);
    }
}
