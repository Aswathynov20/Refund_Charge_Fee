<?php
namespace Egits\RefundChargeFee\Plugin;


use Magento\Sales\Model\Order\CreditmemoService;

class CreditmemoServicePlugin
{
    protected $scopeConfig;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    public function beforeRefund(
        CreditmemoService $subject,
        $creditmemo,
        $offlineRequested = false,
        $refundToStoreCreditAmount = null
    ) {
        // Retrieve configuration values
        $isEnabled = $this->scopeConfig->getValue('refundfee/general/enabled');
        $feeAmount = $this->scopeConfig->getValue('refundfee/refund_charge_fee_configuration/fee_amount');
        $ageThreshold = $this->scopeConfig->getValue('refundfee/refund_charge_fee_configuration/age_threshold');

        // Check if order is free or $0
        if ($creditmemo->getOrder()->getGrandTotal() == 0) {
            return [$creditmemo, $offlineRequested, $refundToStoreCreditAmount];
        }

        // Check if refund amount is greater than order subtotal or available refund amount
        $refundAmount = $creditmemo->getGrandTotal();
        if ($refundAmount >= $creditmemo->getOrder()->getSubtotal() || $refundAmount >= $creditmemo->getOrder()->getTotalRefunded()) {
            return [$creditmemo, $offlineRequested, $refundToStoreCreditAmount];
        }

        // Calculate refund fee and apply additional conditions
        $refundFee = 0;

        if ($isEnabled) {
            if ($ageThreshold > 0) {
                $orderCreatedAt = strtotime($creditmemo->getOrder()->getCreatedAt());
                $currentTime = time();
                $daysDifference = floor(($currentTime - $orderCreatedAt) / (60 * 60 * 24));
                if ($daysDifference > $ageThreshold) {
                    // If the age of the order exceeds the threshold, refund fee is not applicable
                    $refundFee = 0;
                } else {
                    // Apply refund fee based on percentage or amount
                    if ($feeAmount > 0) {
                        // Percentage based fee
                        $refundFee = $refundAmount * ($feeAmount / 100);
                    } else {
                        // Fixed amount fee
                        $refundFee = $feeAmount;
                    }
                }
            }
        }

        // Set refund fee in credit memo
        $creditmemo->setRefundFee($refundFee);

        return [$creditmemo, $offlineRequested, $refundToStoreCreditAmount];
    }
}
