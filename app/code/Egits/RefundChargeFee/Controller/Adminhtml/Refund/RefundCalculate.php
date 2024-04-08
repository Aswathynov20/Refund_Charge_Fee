<?php

/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Egits\RefundChargeFee\Controller\Adminhtml\Refund;

use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Psr\Log\LoggerInterface;
use Magento\Framework\App\Response\Http;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Message\ManagerInterface;

class RefundCalculate extends \Magento\Backend\App\Action
{

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var Json
     */
    protected $serializer;

    /**
     * @var Http
     */
    protected $http;

    /**
     * @var ManagerInterface
     */
    protected $messageManager;

    /**
     * Request instance
     *
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Refund fee configuration path
     */
    const CONFIG_PATH_REFUND_FEE = 'refundfee/general/fee_amount';

    /**
     * Constructor
     *
     * @param LoggerInterface $logger
     * @param RequestInterface $request
     * @param ScopeConfigInterface $scopeConfig
     * @param Http $http
     * @param Json $json
     * @param ManagerInterface $messageManager
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        LoggerInterface $logger,
        RequestInterface $request,
        Http $http,
        Json $json,
        ScopeConfigInterface $scopeConfig,
        ManagerInterface $messageManager
    ) {
        parent::__construct($context);
        $this->logger = $logger;
        $this->request = $request;
        $this->http = $http;
        $this->serializer = $json;
        $this->scopeConfig = $scopeConfig;
        $this->messageManager = $messageManager;
    }

    /**
     * Validate the user credentials
     *
     * @return ResultInterface
     */
    public function execute()
    {
        $isModuleActive = (int) $this->scopeConfig->getValue('refundfee/general/enabled');
        $isRefundable = $this->request->getParam('value');

        if ($isModuleActive) {
            if ($isRefundable) {
                $refundFee = $this->getRefundFee(); // Call a method to calculate the fee

                $response = [
                    'success' => true,
                    'value' => $isRefundable,
                    'refund_fee' => $refundFee,
                    'message' => 'Value recieved and fee calculated.',
                ];
                return $this->jsonResponse($response);
            }
        } else {
            $this->messageManager->addErrorMessage(__('Refund Fee Module is not enabled.'));
        }

        return $this->jsonResponse([]); // Empty response if module not active
    }

    /**
     * Retrieve the configured refund fee amount
     *
     * @return float
     */
    private function getRefundFee(): float
    {
        $feeAmount = (float) $this->scopeConfig->getValue(self::CONFIG_PATH_REFUND_FEE);
        return $feeAmount;
    }

    /**
     * Build an return a json response
     *
     * @param string|mixed $response
     * @return string|mixed
     */
    public function jsonResponse($response = '')
    {
        $this->http->getHeaders()->clearHeaders();
        $this->http->setHeader('Cache-Control', '');
        $this->http->setHeader('Content-Type', 'application/json');
        return $this->http->setBody(
            $this->serializer->serialize($response)
        );
    }
}
