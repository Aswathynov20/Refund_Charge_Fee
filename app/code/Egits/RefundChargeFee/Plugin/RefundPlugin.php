<?php

namespace Egits\RefundChargeFee\Plugin;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

class RefundPlugin
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
     * RefundPlugin constructor.
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
     * Modify the $creditmemo parameter before the refund method is executed.
     *
     * @param \Magento\Sales\Model\Service\CreditmemoService $subject
     * @param \Magento\Sales\Api\Data\CreditmemoInterface $creditmemo
     * @param bool $offlineRequested
     * @return array
     */
    public function beforeRefund(
        \Magento\Sales\Model\Service\CreditmemoService $subject,
        \Magento\Sales\Api\Data\CreditmemoInterface $creditmemo,
        $offlineRequested = false
    ) {
        $isModuleActive = (int) $this->scopeConfig->getValue('refundfee/general/enabled');

        if ($isModuleActive) {

            $refundFee = (int) $this->scopeConfig->getValue('refundfee/refund_charge_fee_configuration/fee_amount');
            $refundAgeThreshold = (int) $this->scopeConfig->getValue('refundfee/refund_charge_fee_configuration/age_threshold');

            $baseGrandTotal = $creditmemo->getBaseGrandTotal();

            $refundFeePercentage = $refundFee / 100;

            $feeAmount = $baseGrandTotal * $refundFeePercentage;

            $newTotal = $baseGrandTotal - $feeAmount;

            $creditmemo->setBaseGrandTotal($feeAmount);

            return [$creditmemo, $offlineRequested];
        } else {
            return [$creditmemo, $offlineRequested];
        }
    }
}
