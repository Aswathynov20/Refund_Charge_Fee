<?php

namespace Egits\RefundChargeFee\Block;

use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Class Utilities is used to hold some common functions
 *
 * @param Egits\RefundChargeFee\Block
 */
class Utilities extends \Magento\Framework\View\Element\Template
{
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Utilities constructor.
     *
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * This function checks if the order is eligible for a refund
     *
     * @return boolean
     */
    public function getIsRefundable()
    {
        $threshold = (int) $this->scopeConfig->getValue('refundfee/refund_charge_fee_configuration/age_threshold');
    }
}
