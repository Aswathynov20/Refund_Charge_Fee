<?php

namespace Egits\RefundChargeFee\Model;

use Magento\Framework\App\Response\Http;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\Encryption\EncryptorInterface as Encryptor;
use Egits\RefundChargeFee\Api\RefundManagementInterface;
use Magento\Framework\Exception\LocalizedException;

class RefundManagement implements RefundManagementInterface
{

    /**
     * @var Encryptor
     */
    private $encryptor;

    /**
     * @var CustomerRegistry
     */
    private $customerRegistry;

    /**
     * @var Authentication
     */
    private $authentication;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var Http
     */
    protected $http;

    /**
     * @var Email
     */
    protected $emailHelper;

    /**
     * @var Json
     */
    protected $serializer;

    /**
     * @var OtpInterface
     */
    protected $otpModel;

    /**
     * @var OtpRepositoryInterface
     */
    protected $otpRepository;

    /**
     * @var ScopeConfigInterface
     */
    protected ScopeConfigInterface $scopeConfig;

    /**
     * TableRepository constructor.
     *
     * @param Json $json
     * @param Encryptor $encryptor
     * @param Http $http
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        Json $json,
        Encryptor $encryptor,
        Http $http,
        ScopeConfigInterface $scopeConfig,
    ) {
        $this->serializer = $json;
        $this->encryptor = $encryptor;
        $this->http = $http;
        $this->scopeConfig = $scopeConfig;
    }


    /**
     * Return module active status
     *
     * @return int
     */
    public function getModuleIsActive()
    {
        return
            (int) $this->scopeConfig->getValue('refundfee/general/enabled');
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

    /**
     * Function calculates the amount to be refunded
     *
     * @return 
     */
    public function calculateRefund()
    {
    }
}
