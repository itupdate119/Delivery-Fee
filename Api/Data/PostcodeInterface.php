<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace B2b\EdiFee\Api\Data;

interface PostcodeInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{

    const PCODE = 'pcode';
    const STATE = 'state';
    const COMMENTS = 'comments';
    const LOCALITY = 'locality';
    const CATEGORY = 'category';
    const POSTCODE_ID = 'postcode_id';

    /**
     * Get postcode_id
     * @return string|null
     */
    public function getPostcodeId();

    /**
     * Set postcode_id
     * @param string $postcodeId
     * @return \B2b\EdiFee\Api\Data\PostcodeInterface
     */
    public function setPostcodeId($postcodeId);

    /**
     * Get pcode
     * @return string|null
     */
    public function getPcode();

    /**
     * Set pcode
     * @param string $pcode
     * @return \B2b\EdiFee\Api\Data\PostcodeInterface
     */
    public function setPcode($pcode);

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \B2b\EdiFee\Api\Data\PostcodeExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     * @param \B2b\EdiFee\Api\Data\PostcodeExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \B2b\EdiFee\Api\Data\PostcodeExtensionInterface $extensionAttributes
    );

    /**
     * Get locality
     * @return string|null
     */
    public function getLocality();

    /**
     * Set locality
     * @param string $locality
     * @return \B2b\EdiFee\Api\Data\PostcodeInterface
     */
    public function setLocality($locality);

    /**
     * Get state
     * @return string|null
     */
    public function getState();

    /**
     * Set state
     * @param string $state
     * @return \B2b\EdiFee\Api\Data\PostcodeInterface
     */
    public function setState($state);

    /**
     * Get comments
     * @return string|null
     */
    public function getComments();

    /**
     * Set comments
     * @param string $comments
     * @return \B2b\EdiFee\Api\Data\PostcodeInterface
     */
    public function setComments($comments);

    /**
     * Get category
     * @return string|null
     */
    public function getCategory();

    /**
     * Set category
     * @param string $category
     * @return \B2b\EdiFee\Api\Data\PostcodeInterface
     */
    public function setCategory($category);
}

