<?php

namespace Egits\RefundChargeFee\Plugin;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Plugin for modifying refund operation behavior.
 */
class RefundOperationPlugin
{
    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Constructor.
     *
     * @param RequestInterface $request
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        RequestInterface $request,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->request = $request;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Plugin to modify refund operation behavior.
     *
     * @param \Magento\Sales\Model\Order\Creditmemo\RefundOperation $subject
     * @param mixed $result
     * @return mixed
     */
    public function afterExecute(
        \Magento\Sales\Model\Order\Creditmemo\RefundOperation $subject,
        $result
    ) {
        // Check if the refund fee module is enabled
        $isModuleActive = (int) $this->scopeConfig->getValue('refundfee/general/enabled');

        // Get the value of refund_fee_value_input parameter from request
        $value = $this->request->getParam('refund_fee_value_input');

        if ($isModuleActive) {
            // Get refund fee and age threshold configuration values
            $refundFee = (int) $this->scopeConfig->getValue('refundfee/refund_charge_fee_configuration/fee_amount');
            $refundAgeThreshold = (int) $this->scopeConfig->
                                                    getValue('refundfee/refund_charge_fee_configuration/age_threshold');

            // Get the grand total of the credit memo
            $grandTotal = $result->getBaseGrandTotal();

            // Check if refund fee is enabled
            $refundFeeEnabled = $this->request->getParam('refund_fee_enabled');

            if ($refundFeeEnabled) {
                // Calculate the total refunded amount based on refund fee percentage
                $baseGrandTotal = $result->getBaseGrandTotal();
                $totalRefunded = $baseGrandTotal / 100 * $refundFee;
                $baseGrandTotal = $baseGrandTotal - $totalRefunded;
                $result->setTotalRefunded($baseGrandTotal);
            }

            return $result;
        } else {
            return $result;
        }
    }
}
