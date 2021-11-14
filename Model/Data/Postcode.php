<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace B2b\EdiFee\Model\Data;

use B2b\EdiFee\Api\Data\PostcodeInterface;

class Postcode extends \Magento\Framework\Api\AbstractExtensibleObject implements PostcodeInterface
{

    /**
     * Get postcode_id
     * @return string|null
     */
    public function getPostcodeId()
    {
        return $this->_get(self::POSTCODE_ID);
    }

    /**
     * Set postcode_id
     * @param string $postcodeId
     * @return \B2b\EdiFee\Api\Data\PostcodeInterface
     */
    public function setPostcodeId($postcodeId)
    {
        return $this->setData(self::POSTCODE_ID, $postcodeId);
    }

    /**
     * Get pcode
     * @return string|null
     */
    public function getPcode()
    {
        return $this->_get(self::PCODE);
    }

    /**
     * Set pcode
     * @param string $pcode
     * @return \B2b\EdiFee\Api\Data\PostcodeInterface
     */
    public function setPcode($pcode)
    {
        return $this->setData(self::PCODE, $pcode);
    }

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \B2b\EdiFee\Api\Data\PostcodeExtensionInterface|null
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * Set an extension attributes object.
     * @param \B2b\EdiFee\Api\Data\PostcodeExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \B2b\EdiFee\Api\Data\PostcodeExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }

    /**
     * Get locality
     * @return string|null
     */
    public function getLocality()
    {
        return $this->_get(self::LOCALITY);
    }

    /**
     * Set locality
     * @param string $locality
     * @return \B2b\EdiFee\Api\Data\PostcodeInterface
     */
    public function setLocality($locality)
    {
        return $this->setData(self::LOCALITY, $locality);
    }

    /**
     * Get state
     * @return string|null
     */
    public function getState()
    {
        return $this->_get(self::STATE);
    }

    /**
     * Set state
     * @param string $state
     * @return \B2b\EdiFee\Api\Data\PostcodeInterface
     */
    public function setState($state)
    {
        return $this->setData(self::STATE, $state);
    }

    /**
     * Get comments
     * @return string|null
     */
    public function getComments()
    {
        return $this->_get(self::COMMENTS);
    }

    /**
     * Set comments
     * @param string $comments
     * @return \B2b\EdiFee\Api\Data\PostcodeInterface
     */
    public function setComments($comments)
    {
        return $this->setData(self::COMMENTS, $comments);
    }

    /**
     * Get category
     * @return string|null
     */
    public function getCategory()
    {
        return $this->_get(self::CATEGORY);
    }

    /**
     * Set category
     * @param string $category
     * @return \B2b\EdiFee\Api\Data\PostcodeInterface
     */
    public function setCategory($category)
    {
        return $this->setData(self::CATEGORY, $category);
    }
}

