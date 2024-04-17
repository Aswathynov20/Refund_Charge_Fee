<?php

namespace Egits\RefundChargeFee\Plugin;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Sales\Api\CreditmemoRepositoryInterface;
use Magento\Sales\Api\OrderRepositoryInterface;

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
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var CreditmemoRepositoryInterface
     */
    protected $creditmemoRepository;

    /**
     * Constructor.
     *
     * @param RequestInterface $request
     * @param ScopeConfigInterface $scopeConfig
     * @param OrderRepositoryInterface $orderRepository
     * @param CreditmemoRepositoryInterface $creditmemoRepository
     */
    public function __construct(
        RequestInterface $request,
        ScopeConfigInterface $scopeConfig,
        OrderRepositoryInterface $orderRepository,
        CreditmemoRepositoryInterface $creditmemoRepository
    ) {
        $this->request = $request;
        $this->scopeConfig = $scopeConfig;
        $this->orderRepository = $orderRepository;
        $this->creditmemoRepository = $creditmemoRepository;
    }

    /**
     * Plugin to modify refund operation behavior.
     *
     * @param mixed $subject
     * @param mixed $result
     * @return mixed
     */
    public function afterExecute(
        $subject,
        $result
    ) {
        $isModuleActive = (int) $this->scopeConfig->getValue('refundfee/general/enabled');

        $value = $this->request->getParam('refund_fee_value_input');

        if ($isModuleActive) {
            $refundFee = (int) $this->scopeConfig->getValue('refundfee/refund_charge_fee_configuration/fee_amount');

            if ($refundFee > 100) {
                $refundFee = 100;
            }

            $refundAgeThreshold = (int) $this->scopeConfig->getValue('refundfee/refund_charge_fee_configuration/age_threshold');

            $grandTotal = $result->getBaseGrandTotal();

            $refundFeeEnabled = $this->request->getParam('refund_fee_enabled');

            if ($refundFeeEnabled) {
                $baseGrandTotal = $result->getBaseGrandTotal();
                $totalRefunded = $baseGrandTotal / 100 * (float) $refundFee;
                $baseGrandTotal = $baseGrandTotal - $totalRefunded;
                $result->setTotalRefunded($baseGrandTotal);
                $this->orderRepository->save($result);
            }

            return $result;
        } else {
            return $result;
        }
    }
}