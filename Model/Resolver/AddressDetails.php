<?php

/**
 * A Magento 2 module named Experius/Postcode
 * Copyright (C) 2016 Experius
 *
 * This file included in Experius/Postcode is licensed under OSL 3.0
 *
 * http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * Please see LICENSE.txt for the full text of the OSL 3.0 license
 */

namespace Experius\PostcodeGraphQl\Model\Resolver;

use Flekto\Postcode\Helper\StoreConfigHelper;
use Flekto\Postcode\Service\PostcodeApiClient;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class AddressDetails implements ResolverInterface
{
    
    protected $postcodeHelper;

    /**
     * PostcodeManagement constructor.
     * @param Settings $helper
     */
    public function __construct(
	PostcodeApiClient $postcodeHelper
    ) {
	$this->postcodeHelper = $postcodeHelper;
    }

    /**
     * @inheritdoc
     */
    public function resolve(
        Field $field,
              $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {

        if (!isset($args['context']) || !$args['context']) {
            throw new GraphQlInputException(__('"context" should be specified'));
        }
        $result = $this->postcodeHelper->internationalGetDetails($args['context']);
        return $result;
    }
}
