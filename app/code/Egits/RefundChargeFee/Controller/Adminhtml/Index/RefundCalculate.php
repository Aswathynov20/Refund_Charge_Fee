<?php

/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Egits\RefundChargeFee\Controller\Adminhtml\Index;

use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Psr\Log\LoggerInterface;
use Magento\Framework\App\Response\Http;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Message\ManagerInterface;

class RefundCalculate extends \Magento\Backend\App\Action implements HttpPostActionInterface
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

                $response = [
                    'success' => true,
                    'value' => $isRefundable,
                    'message' => 'Value recieved.',
                ];
                return $this->jsonResponse($response);
            }
        }
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
