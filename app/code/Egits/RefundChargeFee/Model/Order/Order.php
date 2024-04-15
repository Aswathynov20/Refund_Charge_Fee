<?php
namespace Egits\RefundChargeFee\Model\Order;

use Magento\Framework\Model\AbstractModel;
use Egits\RefundChargeFee\Api\Data\OrderInterface;

class Order extends AbstractModel implements OrderInterface
{
    /**
     * Get Refund Fee
     *
     * @return string|null
     */
    public function getRefundFee()
    {
        return $this->getData(self::REFUND_FEE);
    }

    /**
     * Set Refund Fee
     *
     * @param string|null $refundFee
     * @return $this
     */
    public function setRefundFee($refundFee)
    {
        return $this->setData(self::REFUND_FEE, $refundFee);
    }
}
