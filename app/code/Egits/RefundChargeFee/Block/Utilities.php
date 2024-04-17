<?php

namespace Egits\RefundChargeFee\Block;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Sales\Api\OrderRepositoryInterface;

/**
 * Class Utilities is used to hold some common functions
 *
 */
class Utilities extends \Magento\Framework\View\Element\Template
{
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * Utilities constructor.
     *
     * @param ScopeConfigInterface $scopeConfig
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        OrderRepositoryInterface $orderRepository
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->orderRepository = $orderRepository;
    }

    /**
     * This function checks if the order is eligible for a refund
     *
     * @param int $orderId
     * @return bool
     */
    public function getIsRefundable($orderId)
    {
        $threshold = (int) $this->scopeConfig->getValue('refundfee/refund_charge_fee_configuration/age_threshold');
        $order = $this->orderRepository->get($orderId);
        $totalPaid = (int) $order->getTotalPaid();
        $orderCreatedAt = strtotime($order->getCreatedAt());
        $currentTime = time();
        $differenceInHours = (int) (($currentTime - $orderCreatedAt) / (60 * 60));

        $differenceInDays = floor($differenceInHours / 24);

        $remainingHours = (int) ($differenceInHours % 24);

        if ($totalPaid != 0) {
            if ($differenceInDays > $threshold || ($differenceInDays == $threshold && $remainingHours > 0)) {
                return false;
            } else {
                return true;
            }
        }
    }

    /**
     * Get the refund fee amount from configuration
     *
     * @return float
     */
    public function getRefundFee(): float
    {
        $feeAmount = (float) $this->scopeConfig->getValue('refundfee/refund_charge_fee_configuration/fee_amount');

        if ($feeAmount > 100) {
            $feeAmount = 100;
        }

        return $feeAmount;
    }
}