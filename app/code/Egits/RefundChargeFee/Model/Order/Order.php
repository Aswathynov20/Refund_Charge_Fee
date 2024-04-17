<?php

namespace Egits\RefundChargeFee\Model;

use Egits\RefundChargeFee\Api\Data\OrderInterface;
use Magento\Sales\Model\Order as MagentoOrder;

class Order extends MagentoOrder implements OrderInterface
{
    /**
     * Get Refund Fee
     *
     * @return string|null
     */
    public function getRefundFee()
    {
        // Your custom logic to get refund fee
    }

    /**
     * Set Refund Fee
     *
     * @param float $refundFee
     * @return $this
     */
    public function setRefundFee($refundFee)
    {
        $this->setData('refund_fee', $refundFee);
        return $this;
    }
}
