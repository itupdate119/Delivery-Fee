<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace B2b\EdiFee\Model;

use B2b\EdiFee\Api\Data\PostcodeInterface;
use B2b\EdiFee\Api\Data\PostcodeInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;

class Postcode extends \Magento\Framework\Model\AbstractModel
{

    protected $postcodeDataFactory;

    protected $_eventPrefix = 'b2b_edifee_postcode';
    protected $dataObjectHelper;


    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param PostcodeInterfaceFactory $postcodeDataFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param \B2b\EdiFee\Model\ResourceModel\Postcode $resource
     * @param \B2b\EdiFee\Model\ResourceModel\Postcode\Collection $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        PostcodeInterfaceFactory $postcodeDataFactory,
        DataObjectHelper $dataObjectHelper,
        \B2b\EdiFee\Model\ResourceModel\Postcode $resource,
        \B2b\EdiFee\Model\ResourceModel\Postcode\Collection $resourceCollection,
        array $data = []
    ) {
        $this->postcodeDataFactory = $postcodeDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Retrieve postcode model with postcode data
     * @return PostcodeInterface
     */
    public function getDataModel()
    {
        $postcodeData = $this->getData();
        
        $postcodeDataObject = $this->postcodeDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $postcodeDataObject,
            $postcodeData,
            PostcodeInterface::class
        );
        
        return $postcodeDataObject;
    }
}

