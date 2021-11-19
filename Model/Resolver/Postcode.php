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

use Experius\Core\Helper\Settings;
use Flekto\Postcode\Helper\Exception\ClientException;
use Flekto\Postcode\Helper\PostcodeApiClient;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class Postcode implements ResolverInterface
{

    /**
     * @var PostcodeApiClient
     */
    protected $postcodeHelper;

    /**
     * PostcodeManagement constructor.
     * @param PostcodeApiClient $postcodeHelper
     */
    public function __construct(
        Settings $helper
    ) {
        $this->postcodeHelper = new PostcodeApiClient(
            $helper->getConfigValue('postcodenl_api/general/api_key'),
            $helper->getConfigValue('postcodenl_api/general/api_secret')
        );
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
        if (!isset($args['postcode']) || !$args['postcode']) {
            throw new GraphQlInputException(__('"postcode" should be specified'));
        }
        if (!isset($args['houseNumber']) || !$args['houseNumber']) {
            throw new GraphQlInputException(__('"houseNumber" should be specified'));
        }
        if (!isset($args['houseNumberAddition']) || !$args['houseNumberAddition']) {
            $args['houseNumberAddition'] = '';
        }

        try {
            $result = $this->postcodeHelper->dutchAddressByPostcode($args['postcode'], $args['houseNumber'], $args['houseNumberAddition']);
        } catch (ClientException $e) {
            throw new GraphQlInputException(__($e->getMessage()));
        }

        return $result;
    }
}
