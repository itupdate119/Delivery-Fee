<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace B2b\EdiFee\Model;

use B2b\EdiFee\Api\Data\PostcodeInterfaceFactory;
use B2b\EdiFee\Api\Data\PostcodeSearchResultsInterfaceFactory;
use B2b\EdiFee\Api\PostcodeRepositoryInterface;
use B2b\EdiFee\Model\ResourceModel\Postcode as ResourcePostcode;
use B2b\EdiFee\Model\ResourceModel\Postcode\CollectionFactory as PostcodeCollectionFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Store\Model\StoreManagerInterface;

class PostcodeRepository implements PostcodeRepositoryInterface
{

    protected $dataObjectProcessor;

    protected $extensionAttributesJoinProcessor;

    protected $postcodeCollectionFactory;

    protected $extensibleDataObjectConverter;
    protected $dataPostcodeFactory;

    protected $postcodeFactory;

    private $storeManager;

    private $collectionProcessor;

    protected $dataObjectHelper;

    protected $resource;

    protected $searchResultsFactory;


    /**
     * @param ResourcePostcode $resource
     * @param PostcodeFactory $postcodeFactory
     * @param PostcodeInterfaceFactory $dataPostcodeFactory
     * @param PostcodeCollectionFactory $postcodeCollectionFactory
     * @param PostcodeSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface $collectionProcessor
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     */
    public function __construct(
        ResourcePostcode $resource,
        PostcodeFactory $postcodeFactory,
        PostcodeInterfaceFactory $dataPostcodeFactory,
        PostcodeCollectionFactory $postcodeCollectionFactory,
        PostcodeSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        CollectionProcessorInterface $collectionProcessor,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter
    ) {
        $this->resource = $resource;
        $this->postcodeFactory = $postcodeFactory;
        $this->postcodeCollectionFactory = $postcodeCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataPostcodeFactory = $dataPostcodeFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
        $this->collectionProcessor = $collectionProcessor;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->extensibleDataObjectConverter = $extensibleDataObjectConverter;
    }

    /**
     * {@inheritdoc}
     */
    public function save(
        \B2b\EdiFee\Api\Data\PostcodeInterface $postcode
    ) {
        $postcodeData = $this->extensibleDataObjectConverter->toNestedArray(
            $postcode,
            [],
            \B2b\EdiFee\Api\Data\PostcodeInterface::class
        );

        $postcodeModel = $this->postcodeFactory->create()->setData($postcodeData);

        try {
            $this->resource->save($postcodeModel);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the postcode: %1',
                $exception->getMessage()
            ));
        }
        return $postcodeModel->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function get($postcodeId)
    {
        $postcode = $this->postcodeFactory->create();
        $this->resource->load($postcode, $postcodeId);
        if (!$postcode->getId()) {
            throw new NoSuchEntityException(__('postcode with id "%1" does not exist.', $postcodeId));
        }
        return $postcode->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->postcodeCollectionFactory->create();

        $this->extensionAttributesJoinProcessor->process(
            $collection,
            \B2b\EdiFee\Api\Data\PostcodeInterface::class
        );

        $this->collectionProcessor->process($criteria, $collection);

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);

        $items = [];
        foreach ($collection as $model) {
            $items[] = $model->getDataModel();
        }

        $searchResults->setItems($items);
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(
        \B2b\EdiFee\Api\Data\PostcodeInterface $postcode
    ) {
        try {
            $postcodeModel = $this->postcodeFactory->create();
            $this->resource->load($postcodeModel, $postcode->getPostcodeId());
            $this->resource->delete($postcodeModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the postcode: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($postcodeId)
    {
        return $this->delete($this->get($postcodeId));
    }
}

