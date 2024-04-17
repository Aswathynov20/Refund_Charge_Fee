<?php

namespace Egits\RefundChargeFee\Controller\Adminhtml\Refund;

use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Psr\Log\LoggerInterface;
use Magento\Framework\Pricing\Helper\Data as PriceHelper;
use Magento\Framework\App\Response\Http;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Message\ManagerInterface;
use Magento\Backend\App\Action\Context;
use Egits\RefundChargeFee\Model\Order;

class RefundCalculate extends \Magento\Backend\App\Action
{

    /**
     * @var LoggerInterface
     */
    protected $logger;
    
    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var Http
     */
    protected $http;
    
    /**
     * @var Json
     */
    protected $serializer;

    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

     /**
      * @var ManagerInterface
      */
    protected $messageManager;
     
    /**
     * @var HttpRequest
     */
    protected $httpRequest;

    /**
     * @var PriceHelper
     */
    protected $priceHelper;

    /**
     * Constructor
     *
     * @param Context $context
     * @param LoggerInterface $logger
     * @param RequestInterface $request
     * @param ScopeConfigInterface $scopeConfig
     * @param Http $http
     * @param Json $json
     * @param OrderRepositoryInterface $orderRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param ManagerInterface $messageManager
     * @param HttpRequest $httpRequest
     * @param PriceHelper $priceHelper
     */
    public function __construct(
        Context $context,
        LoggerInterface $logger,
        RequestInterface $request,
        ScopeConfigInterface $scopeConfig,
        Http $http,
        Json $json,
        OrderRepositoryInterface $orderRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        ManagerInterface $messageManager,
        HttpRequest $httpRequest,
        PriceHelper $priceHelper
    ) {
        parent::__construct($context);
        $this->logger = $logger;
        $this->request = $request;
        $this->scopeConfig = $scopeConfig;
        $this->http = $http;
        $this->serializer = $json;
        $this->orderRepository = $orderRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
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
                $refundFee = $this->getRefundFee(); // Retrieve the refund fee from config

                // Calculate total refunded amount
                $baseGrandTotal = $order->getBaseGrandTotal();
                $totalRefunded = $baseGrandTotal / 100 * $refundFee;

                // Store the calculated refund amount in the order
                $this->storeTotalRefunded($order, $totalRefunded);

                // Format total refunded amount as currency
                $totalRefundedCurrency = $this->priceHelper->currency($totalRefunded, true, false);

                $response = [
                    'success' => true,
                    'value' => $isRefundable,
                    'refund_fee' => $refundFee,
                    'totalRefunded' => $totalRefundedCurrency, // Display total refunded amount
                    'message' => 'Value received and fee calculated.
                              Refund fee and total refunded amount stored in the order.',
                ];
                return $this->jsonResponse($response);
            }
        } else {
            $this->messageManager->addErrorMessage(__('Refund Fee Module is not enabled.'));
        }

        return $this->jsonResponse([]); // Empty response if module not active
    }

    /**
     * Retrieve the configured refund fee amount from config
     *
     * @return float
     */
    public function getRefundFee(): float
    {
        $feeAmount = (float) $this->scopeConfig->getValue('refundfee/refund_charge_fee_configuration/fee_amount');
        return $feeAmount;
    }
    
    /**
     * Store the total refunded amount in the order
     *
     * @param \Magento\Sales\Model\Order $order
     * @param float $totalRefunded
     * @return void
     */
    protected function storeTotalRefunded(\Magento\Sales\Model\Order $order, float $totalRefunded)
    {
        try {
            // Set the total refunded amount using a custom setter method
            $order->setRefundFee($totalRefunded);
            $this->orderRepository->save($order);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            $this->messageManager->addErrorMessage(__('Failed to store total refunded amount for order #%1.', $order->getId()));
        }
    }

    

    /**
     * Build and return a json response
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
