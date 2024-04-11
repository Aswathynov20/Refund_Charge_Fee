<?php

namespace Egits\RefundChargeFee\Block;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Sales\Api\OrderRepositoryInterface;

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
     * @return boolean
     */
    public function getIsRefundable($orderId)
    {
        $threshold = (int) $this->scopeConfig->getValue('refundfee/refund_charge_fee_configuration/age_threshold');
        $order = $this->orderRepository->get($orderId);
        $orderCreatedAt = strtotime($order->getCreatedAt());
        $currentTime = time();
        $differenceInDays = floor(($currentTime - $orderCreatedAt) / (60 * 60 * 24));

        if ($differenceInDays > $threshold) {
            return false;
        } else {
            return true;
        }
    }
}
