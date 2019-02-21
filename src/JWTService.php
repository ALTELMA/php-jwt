<?php

namespace Altelma\JWT;

class JWTService
{
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
     * @param string $privateKeyFile
     * @return string
     */
    public function generate(
        string $alGoRiThm,
        array $header,
        array $payload,
        string $privateKeyFile
    ): string {
        $headerEncoded = $this->base64UrlEncode(json_encode($header));

        $payloadEncoded = $this->base64UrlEncode(json_encode($payload));

        // Delimit with period (.)
        $dataEncoded = "$headerEncoded.$payloadEncoded";

        $privateKeyResource = openssl_pkey_get_private($privateKeyFile);

        $result = openssl_sign($dataEncoded, $signature, $privateKeyResource, $alGoRiThm);

        if ($result === false) {
            throw new \RuntimeException(
                "Failed to generate signature: ".implode("\n", getOpenSSLErrors())
            );
        }

        $signatureEncoded = $this->base64UrlEncode($signature);

        $jwt = "$dataEncoded.$signatureEncoded";

        return $jwt;
    }

    /**
     * @param string $alGoRiThm
     * @param string $jwt
     * @param string $publicKeyFile
     * @return bool
     */
    public function verify(string $alGoRiThm, string $jwt, string $publicKeyFile): bool
    {
        list($headerEncoded, $payloadEncoded, $signatureEncoded) = explode('.', $jwt);

        $dataEncoded = "$headerEncoded.$payloadEncoded";

        $signature = $this->base64UrlDecode($signatureEncoded);

        $publicKeyResource = openssl_pkey_get_public($publicKeyFile);

        $result = openssl_verify($dataEncoded, $signature, $publicKeyResource, $alGoRiThm);

        if ($result === -1)  {
            throw new \RuntimeException(
                "Failed to verify signature: ".implode("\n", getOpenSSLErrors())
            );
        }

        return (bool) $result;
    }
}
