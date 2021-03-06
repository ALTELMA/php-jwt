<?php

namespace Altelma\JWT;

class JWTService
{
    /**
     * @var $privateKey
     */
    private $privateKey;

    /**
     * @var $publicKey
     */
    private $publicKey;

    /**
     * JWTService constructor.
     * @param $privateKey
     * @param $publicKey
     */
    public function __construct($privateKey, $publicKey)
    {
        $this->privateKey = !empty($privateKey) ? file_get_contents($privateKey) : null;
        $this->publicKey = !empty($publicKey) ? file_get_contents($publicKey) : null;
    }

    /**
     * @param string $data
     * @return string
     */
    private function base64UrlEncode(string $data) : string
    {
        $urlSafeData = strtr(base64_encode($data), '+/', '-_');

        return rtrim($urlSafeData, '=');
    }

    /**
     * @param string $data
     * @return string
     */
    private function base64UrlDecode(string $data): string
    {
        $urlUnsafeData = strtr($data, '-_', '+/');

        $paddedData = str_pad($urlUnsafeData, strlen($data) % 4, '=', STR_PAD_RIGHT);

        return base64_decode($paddedData);
    }

    /**
     * @param string $alGoRiThm
     * @param array $header
     * @param array $payload
     * @return string
     */
    public function generate(string $alGoRiThm, array $header, array $payload): string
    {
        if (empty($this->privateKey))  {
            throw new \RuntimeException("Failed to generate signature: No private key");
        }

        $headerEncoded = $this->base64UrlEncode(json_encode($header));
        $payloadEncoded = $this->base64UrlEncode(json_encode($payload));
        $dataEncoded = "$headerEncoded.$payloadEncoded";
        $privateKeyResource = openssl_pkey_get_private($this->privateKey);
        $result = openssl_sign($dataEncoded, $signature, $privateKeyResource, $alGoRiThm);

        if ($result === false) {
            throw new \RuntimeException(
                "Failed to generate signature: ".implode("\n", $this->getOpenSSLErrors())
            );
        }

        $signatureEncoded = $this->base64UrlEncode($signature);

        $jwt = "$dataEncoded.$signatureEncoded";

        return $jwt;
    }

    /**
     * @param string $alGoRiThm
     * @param string $jwt
     * @return bool
     */
    public function verify(string $alGoRiThm, string $jwt): bool
    {
        if (empty($this->publicKey))  {
            throw new \RuntimeException("Failed to verify signature: No public key");
        }

        list($headerEncoded, $payloadEncoded, $signatureEncoded) = explode('.', $jwt);

        $dataEncoded = "$headerEncoded.$payloadEncoded";
        $signature = $this->base64UrlDecode($signatureEncoded);
        $publicKeyResource = openssl_pkey_get_public($this->publicKey);
        $result = openssl_verify($dataEncoded, $signature, $publicKeyResource, $alGoRiThm);

        if ($result === -1)  {
            throw new \RuntimeException(
                "Failed to verify signature: ".implode("\n", getOpenSSLErrors())
            );
        }

        return (bool) $result;
    }

    /**
     * @return array
     */
    private function getOpenSSLErrors()
    {
        $messages = [];

        while ($msg = openssl_error_string()) {
            $messages[] = $msg;
        }

        return $messages;
    }
}
