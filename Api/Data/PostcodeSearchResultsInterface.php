<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace B2b\EdiFee\Api\Data;

interface PostcodeSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get postcode list.
     * @return \B2b\EdiFee\Api\Data\PostcodeInterface[]
     */
    public function getItems();

    /**
     * Set pcode list.
     * @param \B2b\EdiFee\Api\Data\PostcodeInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}

