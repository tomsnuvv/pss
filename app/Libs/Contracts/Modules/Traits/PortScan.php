<?php

namespace App\Libs\Contracts\Modules\Traits;

use App\Libs\Helpers\Products;

/**
 * PortScan Trait for Modules
 */
trait PortScan
{
    /**
     * List of ignored products.
     *
     * @var array
     */
    protected $ignored = [
        'server', 'excluded from version scan'
    ];

    /**
     * Checks if a port is open.
     *
     * @param  \SimpleXMLElement $port
     * @return bool
     */
    private function isPortOpen($port)
    {
        if (!isset($port->state->attributes()->state)) {
            return false;
        }
        if ((string) $port->state->attributes()->state !== 'open') {
            return false;
        }

        return true;
    }

    /**
     * Get the service product from a port.
     *
     * @param  \SimpleXMLElement $port
     * @return \App\Models\Product|void
     */
    private function getPortProduct($port)
    {
        if (!isset($port->service->attributes()->product[0])) {
            return;
        }

        $code = (string) $port->service->attributes()->product[0];

        if (!$this->isValidCode($code)) {
            return;
        }

        return Products::createServiceProduct($code);
    }

    /**
     * Checks if a product code is allowed.
     *
     * @param  string $code
     * @return bool
     */
    protected function isValidCode($code)
    {
        return !in_array(strtolower($code), $this->ignored);
    }

    /**
     * Get the service version from a port.
     *
     * @param  \SimpleXMLElement $port
     * @return string|void
     */
    private function getPortProductVersion($port)
    {
        if (!isset($port->service->attributes()->version[0])) {
            return;
        }

        return (string) $port->service->attributes()->version[0];
    }

    /**
     * Get the protocol from a port.
     *
     * @param  \SimpleXMLElement $port
     * @return string|void
     */
    private function getPortProtocol($port)
    {
        if (!isset($port->attributes()->protocol[0])) {
            return;
        }

        return (string) $port->attributes()->protocol[0];
    }

    /**
     * Get the port number from a port.
     *
     * @param  \SimpleXMLElement $port
     * @return int|void
     */
    private function getPortNumber($port)
    {
        if (!isset($port->attributes()->portid[0])) {
            return;
        }

        return (string) $port->attributes()->portid[0];
    }

    /**
     * Get the service name from a port.
     *
     * @param  \SimpleXMLElement $port
     * @return string|void
     */
    private function getPortServiceName($port)
    {
        if (!isset($port->service->attributes()->name[0])) {
            return;
        }

        return (string) $port->service->attributes()->name[0];
    }
}
