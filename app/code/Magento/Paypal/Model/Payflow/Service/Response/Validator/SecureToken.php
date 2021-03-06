<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Paypal\Model\Payflow\Service\Response\Validator;

use Magento\Framework\Object;
use Magento\Paypal\Model\Payflow\Service\Response\ValidatorInterface;

/**
 * Class SecureToken
 */
class SecureToken implements ValidatorInterface
{
    /**
     * Secure Token Error: Secure Token already been used
     */
    const ST_ALREADY_USED = 160;

    /**
     * Secure Token Error: Transaction using secure token is already in progress
     */
    const ST_TRANSACTION_IN_PROCESS = 161;

    /**
     * Secure Token Error: Secure Token Expired
     */
    const ST_EXPIRED = 162;

    /**
     * Validate data
     *
     * @param Object $data
     * @return bool
     */
    public function validate(Object $response)
    {
        return (bool) $response->getSecuretoken()
            && is_numeric($response->getResult())
            && !in_array(
                $response->getResult(),
                [
                    static::ST_ALREADY_USED,
                    static::ST_TRANSACTION_IN_PROCESS,
                    static::ST_EXPIRED,
                ]
            );
    }
}
