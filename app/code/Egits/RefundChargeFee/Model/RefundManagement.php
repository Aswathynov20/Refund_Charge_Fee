<?php

namespace Egits\RefundChargeFee\Model;

use Egits\RefundChargeFee\Api\Data\OrderInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\App\Response\Http;

class RefundManagement
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
     * @var string|null
     */
    protected $refundFee;

    /**
     * RefundManagement constructor.
     *
     * @param Json $json
     * @param ScopeConfigInterface $scopeConfig
     * @param Http $http
     */
    public function __construct(
        Json $json,
        ScopeConfigInterface $scopeConfig,
        Http $http
    ) {
        $this->serializer = $json;
        $this->scopeConfig = $scopeConfig;
        $this->http = $http;
    }

    /**
     * Return module active status
     *
     * @return int
     */
    public function getModuleIsActive()
    {
        return (int) $this->scopeConfig->getValue('refundfee/general/enabled');
    }

    /**
     * Build and return a JSON response
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
