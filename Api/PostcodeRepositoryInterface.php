<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace B2b\EdiFee\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface PostcodeRepositoryInterface
{

    /**
     * Save postcode
     * @param \B2b\EdiFee\Api\Data\PostcodeInterface $postcode
     * @return \B2b\EdiFee\Api\Data\PostcodeInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \B2b\EdiFee\Api\Data\PostcodeInterface $postcode
    );

    /**
     * Retrieve postcode
     * @param string $postcodeId
     * @return \B2b\EdiFee\Api\Data\PostcodeInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($postcodeId);

    /**
     * Retrieve postcode matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \B2b\EdiFee\Api\Data\PostcodeSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete postcode
     * @param \B2b\EdiFee\Api\Data\PostcodeInterface $postcode
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \B2b\EdiFee\Api\Data\PostcodeInterface $postcode
    );

    /**
     * Delete postcode by ID
     * @param string $postcodeId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($postcodeId);
}

