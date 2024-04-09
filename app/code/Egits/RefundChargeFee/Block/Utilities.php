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
     * 
     */
    public function getIsRefundable()
    {
    }
}
