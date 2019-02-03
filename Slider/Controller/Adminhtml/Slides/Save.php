<?php

namespace Excellence\Slider\Controller\Adminhtml\Slides;

class Save extends \Magento\Backend\App\Action
{
    /**
    * @var \Magento\Framework\Image\AdapterFactory
    */
    protected $adapterFactory;
    /**
    * @var \Magento\MediaStorage\Model\File\UploaderFactory
    */
    protected $uploader;
    /**
    * @var \Magento\Framework\Filesystem
    */
    protected $filesystem;
    protected $storeManager;

    protected $resultPageFactory = false;
    protected $resultRedirectFactory = false;
    protected $_slideFactory = false;
    protected $_fileUploaderFactory = false;
    
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory,
        \Magento\Framework\Image\AdapterFactory $adapterFactory,
        \Magento\MediaStorage\Model\File\UploaderFactory $uploader,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Store\Model\ StoreManagerInterface $storeManager,
        \Excellence\Slider\Model\SlidesFactory $slideFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->_fileUploaderFactory = $fileUploaderFactory;
        $this->resultRedirectFactory = $context->getResultRedirectFactory();
        $this->_slideFactory = $slideFactory;

        $this->adapterFactory = $adapterFactory;
        $this->uploader = $uploader;
        $this->filesystem = $filesystem;
        $this->storeManager = $storeManager;
    }

    public function execute()
    {
        //start block upload image
        if (isset($_FILES['image']) && isset($_FILES['image']['name']) && strlen($_FILES['image']['name'])) {
            
        /*
        * Save image upload
        */
            try {

                $base_media_path = 'slides/';
                $uploader = $this->uploader->create(
                    ['fileId' => 'image']
                );
                $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
                $imageAdapter = $this->adapterFactory->create();
                $uploader->addValidateCallback('image', $imageAdapter, 'validateUploadFile');
                $uploader->setAllowRenameFiles(true);
                $uploader->setFilesDispersion(true);
                $mediaDirectory = $this->filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
                $result = $uploader->save(
                $mediaDirectory->getAbsolutePath($base_media_path)
                );
               
                $data['image'] = $base_media_path.$result['file'];
                $filename = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA).$data['image'];
            } catch (\Exception $e) {
                if ($e->getCode() == 0) {
                    $this->messageManager->addError($e->getMessage());
                }
            }
        } else {
            if (isset($data['image']) && isset($data['image']['value'])) {
                if (isset($data['image']['delete'])) {
                    $data['image'] = null;
                    $data['delete_image'] = true;
                } elseif (isset($data['image']['value'])) {
                    $data['image'] = $data['image']['value'];
                } else {
                    $data['image'] = null;
                }
            }
        }
        //end block upload image
        $post = $this->getRequest()->getPostValue();
        if(isset($post)){
            $model = $this->_slideFactory->create();
            if(!empty($post['id'])){
                $model->load($post['id']);
            }
            if(empty($post['image_position'])){
                $post['image_position'] == 0;
            }
            $model->setTitle($post['title']);
            $model->setImageName($post['image_name']);
            if(empty($post['image_position'])){
                $model->setImagePosition(0);
            }
            else{
                $model->setImagePosition($post['image_position']);
            }
            $model->setIsActive($post['is_active']);
            $model->setSliderName($post['slider_name']);
            $model->setContent($post['content']);
            if(!empty($filename)){
                $model->setFilename($filename);
            }
            if($model->save()){
                $this->messageManager->addSuccess(__("Slide <strong>".$model->getImageName()."</strong> Has Been Saved."));
            }
            else{
                $this->messageManager->addError(__('Some error occured while saving the slide. Please try again.'));
            }
            if ($this->getRequest()->getParam('back')) {
                $this->_redirect('*/*/edit', ['id' => $model->getId(), '_current' => true]);
                return;
            }
        }
        


        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('*/*/');
        return $resultRedirect;
    }
}