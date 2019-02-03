<?php
namespace Excellence\Slider\Block;

use Excellence\Slider\Model\Adminhtml\Config\Source\Status;
use Excellence\Slider\Model\Adminhtml\Config\Source\Pages;
use Excellence\Slider\Model\ResourceModel\Slides\CollectionFactory;
use Excellence\Slider\Model\SliderpagesFactory;
  
class Slider extends \Magento\Framework\View\Element\Template
{   
	protected $_collectionFactory;
    protected $_sliderPagesFactory;
    protected $_scopeConfigObject;
    protected $_page;
    protected $_registry;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigObject,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        CollectionFactory $collectionFactory,
        SliderpagesFactory $sliderPagesFactory,
        \Magento\Cms\Model\Page $page,
        \Magento\Framework\Registry $registry,
        array $data = []
    )
    {
        $this->_scopeConfigObject = $scopeConfigObject;
        $this->_storeManager = $storeManager;
    	$this->_collectionFactory = $collectionFactory;
        $this->_sliderPagesFactory = $sliderPagesFactory;
        $this->_page = $page;
        $this->_registry = $registry;
        parent::__construct($context, $data);
    }
    public function getStretchImageInfo($sliderId)
    {
        if(!empty($sliderId)){
            $sliderDetails =  $this->_sliderPagesFactory->create()->getCollection()->addFieldToFilter('id',$sliderId);
            foreach ($sliderDetails as $sliderDataInfo) {
               $stretchImageVal = $sliderDataInfo->getStretchImage();
            }
            return $stretchImageVal;
        }
    }
    public function setSlider($position)
    {
        if(!($this->_scopeConfigObject->getValue('slider/slider/enable'))){
            return;
        }
        $storeId = $this->_storeManager->getStore()->getId();
        $sliderId = $this->_sliderPagesFactory->create();
        if(!empty($this->_registry->registry('current_category'))){
            //category page
            $category = $this->_registry->registry('current_category');
            $slider_id = $sliderId->getSliderId(Pages::CATEGORY_PAGE, $category->getID(), $position, $storeId);
        }    
        if(!empty($this->_registry->registry('current_product'))){
            //product page
            $product = $this->_registry->registry('current_product');
            $slider_id = $sliderId->getSliderId(Pages::PRODUCT_PAGE, $product->getID(), $position, $storeId);
        }
        if(!empty($this->_page->getId())){
            $slider_id = $sliderId->getSliderId(Pages::CMS_PAGE, $this->_page->getId(), $position, $storeId);
            //cms page
        }
        if(!empty($slider_id)){
            $slides = $this->_collectionFactory->create()
                        ->addFieldToFilter('is_active', Status::STATUS_ENABLED)
                        ->addFieldToFilter('slider_name', $slider_id)
                        ->setOrder('image_position', 'ASC');
            $collectionData = $slides->getData();
        }
        else{
            $collectionData = null;
        }
        
        $this->setSlidesModel($collectionData);
        $sliderType = $this->_scopeConfigObject->getValue('slider/slider/select_slider');
        $this->setSliderType($sliderType);
        $this->setSliderPositionClass($this->getSliderClass($position));
    }



    public function checkoutPageSlider($pageTypeId, $position)
    {
        if(!($this->_scopeConfigObject->getValue('slider/slider/enable'))){
            return;
        }
        $storeId = $this->_storeManager->getStore()->getId();
        $sliderId = $this->_sliderPagesFactory->create();
        if($pageTypeId == Pages::CART_PAGE){
          $slider_id = $sliderId->getSliderId(Pages::CART_PAGE,'', $position, $storeId);
        }
        if($pageTypeId == Pages::CHECKOUT_PAGE){
          $slider_id = $sliderId->getSliderId(Pages::CHECKOUT_PAGE,'', $position, $storeId);
        }
        $slides = $this->_collectionFactory->create()
                        ->addFieldToFilter('is_active', Status::STATUS_ENABLED)
                        ->addFieldToFilter('slider_name', $slider_id)
                        ->setOrder('image_position', 'ASC');;
        $collectionData = $slides->getData();
        $this->setSlidesModel($collectionData);
        $sliderType = $this->_scopeConfigObject->getValue('slider/slider/select_slider');
        $this->setSliderType($sliderType);
        $this->setSliderPositionClass($this->getSliderClass($position));
    }
    public function getSliderClass($position){
        $positionArray = [['value' => 1, 'class' => 'top-left'], 
                ['value' => 2, 'class' => 'bottom-left'],
                ['value' => 3, 'class' => 'top-center'],
                ['value' => 4, 'class' => 'bottom-center'],
                ['value' => 5, 'class' => 'top-right'],
                ['value' => 6, 'class' => 'bottom-right']
                ];
        $sliderContainerClass = '';
        foreach ($positionArray as $positionData) {
            if($positionData['value'] == $position){
                $sliderContainerClass = $positionData['class'];
                break;
            }
        }
        return $sliderContainerClass;
    }
}