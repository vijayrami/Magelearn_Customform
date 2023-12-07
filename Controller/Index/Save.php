<?php

namespace Magelearn\Customform\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magelearn\Customform\Model\CustomformFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Framework\Image\AdapterFactory;
use Magento\Framework\Filesystem;

class Save extends \Magento\Framework\App\Action\Action
{
	/**
     * @var Customform
     */
    protected $_customform;
    protected $uploaderFactory;
    protected $adapterFactory;
    protected $filesystem;

    public function __construct(
		Context $context,
        CustomformFactory $customform,
        UploaderFactory $uploaderFactory,
        AdapterFactory $adapterFactory,
        Filesystem $filesystem
    ) {
        $this->_customform = $customform;
        $this->uploaderFactory = $uploaderFactory;
        $this->adapterFactory = $adapterFactory;
        $this->filesystem = $filesystem;
        parent::__construct($context);
    }
	public function execute()
    {
    	if (!$this->getRequest()->isPost()) {
            return $this->resultRedirectFactory->create()->setPath('*/*/');
        }
        //$data = $this->getRequest()->getParams();
        $data = $this->validatedParams();
        if(isset($_FILES['image']['name']) && $_FILES['image']['name'] != '') {
            try{
                $uploaderFactory = $this->uploaderFactory->create(['fileId' => 'image']);
                $uploaderFactory->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
                $imageAdapter = $this->adapterFactory->create();
                $uploaderFactory->addValidateCallback('custom_image_upload',$imageAdapter,'validateUploadFile');
                $uploaderFactory->setAllowRenameFiles(true);
                $uploaderFactory->setFilesDispersion(true);
                $mediaDirectory = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);
                $destinationPath = $mediaDirectory->getAbsolutePath('magelearn/customform');
                $result = $uploaderFactory->save($destinationPath);
                if (!$result) {
                    throw new LocalizedException(
                        __('File cannot be saved to path: $1', $destinationPath)
                    );
                }
                
                $imagePath = 'magelearn/customform'.$result['file'];
                $data['image'] = $imagePath;
            } catch (\Exception $e) {
            }
        }
    	$customform = $this->_customform->create();
        $customform->setData($data);
        if($customform->save()){
            $this->messageManager->addSuccessMessage(__('You saved the data.'));
        }else{
            $this->messageManager->addErrorMessage(__('Data was not saved.'));
        }
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('customform');
        return $resultRedirect;
    }
/**
     * @return array
     * @throws \Exception
     */
    private function validatedParams()
    {
        $request = $this->getRequest();
        if (trim($request->getParam('first_name')) === '') {
            throw new LocalizedException(__('Enter the First Name and try again.'));
        }
		if (trim($request->getParam('last_name')) === '') {
            throw new LocalizedException(__('Enter the Last Name and try again.'));
        }
		if (false === \strpos($request->getParam('email'), '@')) {
            throw new LocalizedException(__('The email address is invalid. Verify the email address and try again.'));
        }
		if (trim($request->getParam('phone')) === '') {
            throw new LocalizedException(__('Enter the Phone Number and try again.'));
        }
		if (trim($request->getParam('message')) === '') {
            throw new LocalizedException(__('Enter your message and try again.'));
        }
        return $request->getParams();
    }
}
