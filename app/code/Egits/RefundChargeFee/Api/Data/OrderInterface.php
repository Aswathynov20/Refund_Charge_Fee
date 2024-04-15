<?php
namespace Egits\RefundChargeFee\Api\Data;

interface OrderInterface
{
    public const REFUND_FEE = 'refund_fee';

    /**
     * Get Refund Fee
     *
     * @return string|null
     */
    public function getRefundFee();

    /**
     * Set Refund Fee
     *
     * @param string|null $refundFee
     * @return $this
     */
    public function setRefundFee($refundFee);
}
