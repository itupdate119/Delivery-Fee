<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace B2b\EdiFee\Model\ResourceModel\Postcode;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * @var string
     */
    protected $_idFieldName = 'postcode_id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \B2b\EdiFee\Model\Postcode::class,
            \B2b\EdiFee\Model\ResourceModel\Postcode::class
        );
    }
}

