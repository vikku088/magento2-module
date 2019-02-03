<?php

namespace Excellence\Slider\Model\Adminhtml\Config\Source;
 
class Pages implements \Magento\Framework\Option\ArrayInterface
{
    const CMS_PAGE = 1;
    const CATEGORY_PAGE = 2;
    const PRODUCT_PAGE = 3;
    const CHECKOUT_PAGE = 4;
    const CART_PAGE = 5;
    const WIDGET_PAGE = 6;


    public function toOptionArray()
    {

        return [['value' => null, 'label' => __('-- Select Page --')],
                ['value' => self::CMS_PAGE, 'label' => __('CMS Page')], 
                ['value' => self::CATEGORY_PAGE, 'label' => __('Category Page')],
                ['value' => self::PRODUCT_PAGE, 'label' => __('Product Page')],
                ['value' => self::CHECKOUT_PAGE, 'label' => __('Checkout Page')],
                ['value' => self::CART_PAGE, 'label' => __('Cart Page')],
                ['value' => self::WIDGET_PAGE, 'label' => __('Widget Page')]
                ];
                
    }   
}