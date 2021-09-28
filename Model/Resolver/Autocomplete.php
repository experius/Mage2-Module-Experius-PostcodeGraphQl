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
use Flekto\Postcode\Helper\CountryCodeConvertorHelper;
use Flekto\Postcode\Helper\PostcodeApiClient;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class Autocomplete implements ResolverInterface
{

    /**
     * @var PostcodeApiClient
     */
    protected $postcodeHelper;

    /**
     * PostcodeManagement constructor.
     * @param Settings $helper
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
        if (!isset($args['countryId']) || !$args['countryId']) {
            throw new GraphQlInputException(__('"countryId" should be specified'));
        }
        if (!isset($args['searchTerm']) || !$args['searchTerm']) {
            throw new GraphQlInputException(__('"searchTerm" should be specified'));
        }
        $countryId = CountryCodeConvertorHelper::alpha2ToAlpha3($args['countryId']);
        if(!isset($args['xAutocompleteSession'])) {
            $session = bin2hex(random_bytes(8));
        } else {
            $session = $args['xAutocompleteSession'];
        }

        $result = $this->postcodeHelper->internationalAutocomplete($countryId, $args['searchTerm'], null, 'en-US');
        if (isset($result['matches'])) {
            foreach($result['matches'] as &$match) {
                if(isset($match['highlights'])) {
                    foreach($match['highlights'] as &$highlight) {
                        $highlight['start'] = $highlight[0];
                        $highlight['end'] = $highlight[1];
                    }
                }
            }
        }
        return $result;
    }
}
