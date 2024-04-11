<?php

/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Egits\RefundChargeFee\Controller\Adminhtml\Refund;

use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\ResultInterface;
// use Egits\RefundChargeFee\Api\RefundManagementInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\LocalizedException;
use Psr\Log\LoggerInterface;
use Magento\Framework\Pricing\Helper\Data as PriceHelper;
use Magento\Framework\App\Response\Http;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Message\ManagerInterface;

class RefundCalculate extends \Magento\Backend\App\Action
{

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var PriceHelper
     */
    protected $priceHelper;

    /**
     * @var HttpRequest
     */
    protected $httpRequest;

    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    // /**
    //  * @var RefundManagementInterface
    //  */
    // protected $refundManager;

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
     * @param OrderRepositoryInterface $orderRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param RefundManagementInterface $refundManager
     * @param ManagerInterface $messageManager
     * @param HttpRequest $httpRequest
     * @param PriceHelper $priceHelper
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        LoggerInterface $logger,
        RequestInterface $request,
        Http $http,
        Json $json,
        OrderRepositoryInterface $orderRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        // RefundManagementInterface $refundManager,
        ScopeConfigInterface $scopeConfig,
        ManagerInterface $messageManager,
        HttpRequest $httpRequest,
        PriceHelper $priceHelper
    ) {
        parent::__construct($context);
        $this->logger = $logger;
        $this->request = $request;
        $this->http = $http;
        $this->serializer = $json;
        $this->orderRepository = $orderRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        // $this->refundManager = $refundManager;
        $this->scopeConfig = $scopeConfig;
        $this->messageManager = $messageManager;
        $this->httpRequest = $httpRequest;
        $this->priceHelper = $priceHelper;
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
        $orderId = (int) $this->request->getParam('orderId');

        try {
            // Retrieve the order using the orderId
            $order = $this->orderRepository->get($orderId);
        } catch (\Exception $e) {
            // $this->messageManager->addErrorMessage(__('Error occurred while retrieving the order.'));
            return $this->jsonResponse([]);
        }


        if ($isModuleActive) {
            if ($isRefundable) {
                $refundFee = $this->getRefundFee(); // Call a method to calculate the fee

                $baseGrandTotal = $order->getBaseGrandTotal();
                $totalRefunded = $baseGrandTotal / 100 * $refundFee;

                // $totalRefunded = $baseGrandTotal - $refundFee;

                $totalRefundedCurrency = $this->priceHelper->currency($totalRefunded, true, false);

                $response = [
                    'success' => true,
                    'value' => $isRefundable,
                    'refund_fee' => $refundFee,
                    'totalRefunded' => $totalRefundedCurrency,
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
        $feeAmount = (float) $this->scopeConfig->getValue('refundfee/refund_charge_fee_configuration/fee_amount');
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
