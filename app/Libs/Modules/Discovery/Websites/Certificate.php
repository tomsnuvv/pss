<?php

namespace App\Libs\Modules\Discovery\Websites;

use App\Libs\Contracts\Modules\Abstracts\Module;
use App\Models\Certificate as CertificateModel;
use Illuminate\Support\Arr;
use App\Libs\Helpers\Hosts;
use App\Libs\Helpers\Domains;
use \Exception;

/**
 * Certificate Websites Discovery Module.
 *
 * Obtains the Certificate information from a website.
 *
 * @todo Add the Port 443 used for the connection.
 */
class Certificate extends Module
{
    /**
     * {@inheritdoc}
     */
    protected function canRun()
    {
        if ($this->ranInLastHours(168)) {
            $this->setMessage('Module already executed in the last week');
            return false;
        }

        if (!strstr($this->model->url, 'https://')) {
            $this->setMessage('Website is not using SSL.');
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        try {
            $data = $this->getCertificate();
        } catch (Exception $e) {
            if (strstr($e->getMessage(), 'timed out')) {
                $this->setMessage('Connection timed out');
                return;
            }
            if (strstr($e->getMessage(), 'Name or service not known')) {
                $this->setMessage('Name or service not known');
                return;
            }
            if (strstr($e->getMessage(), 'No address associated with hostname')) {
                $this->setMessage('No address associated with hostname');
                return;
            }
            throw $e;
        }
            throw $e;
        }
        $this->store($data);
        $this->showOutput();
    }

    /**
     * Get the certificate information.
     *
     * @return array|null
     */
    private function getCertificate()
    {
        $parse = parse_url($this->model->url);
        $hostname = $parse['host'];
        $port = isset($parse['port']) ? $parse['port'] : 443;

        $stream = stream_socket_client(
            "ssl://" . $hostname . ":" . $port,
            $errno,
            $errstr,
            10,
            STREAM_CLIENT_CONNECT,
            stream_context_create([
                    'ssl' => [
                        'capture_peer_cert' => true,
                        'allow_self_signed' => true,
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                    ]
                ])
        );

        $context = stream_context_get_params($stream);
        $key = openssl_pkey_get_public($context["options"]["ssl"]["peer_certificate"]);

        return [
            'cert' => openssl_x509_parse($context['options']['ssl']['peer_certificate']),
            'key' => openssl_pkey_get_details($key),
        ];
    }

    /**
     * Store the obtained data.
     *
     * @param array $data
     */
    private function store($data)
    {
        $certificate = CertificateModel::firstOrNew([
            'serial' => $data['cert']['serialNumber']
        ]);

        $map = [
            'name' => 'name',
            'subject_org_unit' => 'subject.OU',
            'subject_common_name' => 'subject.CN',
            'issuer_org' => 'issuer.O',
            'issuer_common_name' => 'issuer.CN',
            'issuer_country' => 'issuer.C',
            'issuer_county' => 'issuer.ST',
            'issuer_locality' => 'issuer.L',
            'signature_algorithm' => 'signatureTypeSN',
            'creation_date' => 'validFrom_time_t',
            'expiration_date' => 'validTo_time_t',
        ];
        foreach ($map as $key => $value) {
            $certificate->$key = $this->extractField($data['cert'], $value);
        }

        // Key
        $certificate->key_length = $this->extractField($data['key'], 'bits');
        $keyType = 'Unknown';
        if (isset($data['key']['rsa'])) {
            $keyType = 'RSA';
        } elseif (isset($data['key']['ec'])) {
            $keyType = 'EC';
        } elseif (isset($data['key']['dsa'])) {
            $keyType = 'DSA';
        } elseif (isset($data['key']['dh'])) {
            $keyType = 'DH';
        }
        $certificate->key_type = $keyType;

        $certificate->save();

        $hostname = parse_url($this->model->url, PHP_URL_HOST);
        if (filter_var($hostname, FILTER_VALIDATE_IP)) {
            $host = Hosts::createServerFromIP($hostname);
            if ($host) {
                $host->certificates()->syncWithoutDetaching([$certificate->id]);
            }
        } else {
            $domain = Domains::createDomain($hostname);
            if ($domain) {
                $domain->certificate()->associate($certificate);
                $domain->save();
            }
        }

        $this->items[] = $certificate;
    }

    /**
     * Extract a field from the Certificate data.
     * @param  array  $data  Certificate data
     * @param  string $field Field
     * @return string|void
     */
    private function extractField($data, $field)
    {
        if (!Arr::has($data, $field)) {
            return;
        }
        $value = data_get($data, $field);
        if (is_array($value)) {
            $value = implode(' ', $value);
        }
        return $value;
    }

    /**
     * Display the obtained data.
     */
    private function showOutput()
    {
        foreach ($this->items as $item) {
            foreach ($item->toArray() as $field => $value) {
                if ($item->isFillable($field)) {
                    $this->outputDetail($field, $value);
                }
            }
        }
    }
}
