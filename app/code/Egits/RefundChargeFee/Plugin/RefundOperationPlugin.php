<?php
namespace Egits\RefundChargeFee\Plugin;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

class RefundOperationPlugin
{
    protected $request;
    protected $scopeConfig;

    public function __construct(
        RequestInterface $request,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->request = $request;
        $this->scopeConfig = $scopeConfig;
    }

    public function afterExecute(
        \Magento\Sales\Model\Order\Creditmemo\RefundOperation $subject,
        $result
    ) {
        $isModuleActive = (int) $this->scopeConfig->getValue('refundfee/general/enabled');

        if ($isModuleActive) {
            $refundFee = (int) $this->scopeConfig->getValue('refundfee/refund_charge_fee_configuration/fee_amount');
            $refundAgeThreshold = (int) $this->scopeConfig->getValue('refundfee/refund_charge_fee_configuration/age_threshold');

            $grandTotal = $result->getBaseGrandTotal();
            $refundFeeEnabled = $this->request->getParam('refund_fee_enabled');
            
            if ($refundFeeEnabled) {
                
                $totalRefunded = $grandTotal - $refundFee;
                $result->setTotalRefunded($totalRefunded);
            }

            return $result;
        } else {
            return $result;
        }
    }
}