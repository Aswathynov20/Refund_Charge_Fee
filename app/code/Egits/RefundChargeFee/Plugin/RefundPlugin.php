<?php

namespace Egits\RefundChargeFee\Plugin;

use Magento\Framework\App\RequestInterface;
use Magento\Sales\Controller\Adminhtml\Order\Creditmemo\Save as CreditmemoSaveController;

class RefundPlugin
{
    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * RefundPlugin constructor.
     * @param RequestInterface $request
     */
    public function __construct(
        RequestInterface $request
    ) {
        $this->request = $request;
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

        $newGrandTotal = $creditmemo->getBaseGrandTotal() - 10;
        $creditmemo->setBaseGrandTotal($newGrandTotal);


        return [$creditmemo, $offlineRequested];
    }
}
