<?php

namespace Egits\RefundChargeFee\Api;

interface RefundManagementInterface
{

    /**
     * Return module active status
     *
     * @return int
     */
    public function getModuleIsActive();

    /**
     * Build an return a json response
     *
     * @param string|mixed $response
     * @return string|mixed
     */
    public function jsonResponse($response = '');
}
