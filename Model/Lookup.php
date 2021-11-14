<?php

namespace B2b\EdiFee\Model;

class Lookup
{
    /**
     * @var ResourceModel\Postcode\CollectionFactory
     */
    protected $postcodeCollection;

    /**
     * @param ResourceModel\Postcode\CollectionFactory $postcodeCollection
     */
    public function __construct(
        \B2b\EdiFee\Model\ResourceModel\Postcode\CollectionFactory $postcodeCollection
    ) {
        $this->postcodeCollection = $postcodeCollection;

    }

    public function lookupByPostcode($query, $limit = 10) {
        $collection = $this->postcodeCollection->create();
        $select = $collection->getSelect();
        $select->limit($limit);
        $select->where(new \Zend_Db_Expr("pcode LIKE '%$query%'"));
        $select->reset('columns');
        $select->reset('order');
        $select->columns(['pcode', 'locality', 'state']);
        $data =  $collection->getConnection()->fetchAssoc($select);

        return $data;
    }
}
