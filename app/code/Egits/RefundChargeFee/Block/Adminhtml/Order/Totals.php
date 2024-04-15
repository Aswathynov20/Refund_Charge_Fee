<?php
namespace Egits\RefundChargeFee\Block\Adminhtml\Order;

class Totals extends \Magento\Sales\Block\Adminhtml\Order\Totals
{
    /**
     * Initialize order totals array
     *
     * @return $this
     */
    protected function _initTotals()
    {
        parent::_initTotals();

        // Retrieve the order object
        $order = $this->getSource();

        // Retrieve the refund fee from the order object
        $refundFee = $order->getData('refund_fee');

        // Override the existing totals or add new ones as needed
        $this->_totals['refund'] = new \Magento\Framework\DataObject(
            [
                'code' => 'refund',
                'strong' => true,
                'value' => $refundFee, // Use the refund fee value
                'base_value' => $refundFee, // Use the base refund fee value
                'label' => __('Refund Fee'),
                'area' => 'footer',
            ]
        );

        return $this;
    }
}
