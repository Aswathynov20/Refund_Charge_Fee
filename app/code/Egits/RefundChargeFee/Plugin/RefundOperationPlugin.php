<?php

namespace Egits\RefundChargeFee\Plugin;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

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
     * After plugin for the authenticate method.
     *
     * @param \Magento\Customer\Model\Authentication $subject
     * @param \Magento\Customer\Model\Customer $result
     * @return \Magento\Customer\Model\Customer
     * @throws \Exception
     */
    public function afterExecute(
        \Magento\Sales\Model\Order\Creditmemo\RefundOperation $subject,
        $result
    ) {
        $isModuleActive = (int) $this->scopeConfig->getValue('refundfee/general/enabled');

        if ($isModuleActive) {

            $refundFee = (int) $this->scopeConfig->getValue('refundfee/refund_charge_fee_configuration/fee_amount');
            $refundAgeThreshold = (int) $this->scopeConfig->getValue('refundfee/refund_charge_fee_configuration/age_threshold');

            $total = (string) ($result->getBaseGrandTotal() - 10);
            var_dump($total);

            $result->setTotalRefunded($total);

            $result->getTotalRefunded();

            return $result;
        } else {
            return $result;
        }
    }
}
