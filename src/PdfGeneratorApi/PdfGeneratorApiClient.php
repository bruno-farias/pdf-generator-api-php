<?php


namespace PdfGeneratorApi;


use Firebase\JWT\JWT;
use GuzzleHttp\Client;

class PdfGeneratorApiClient implements ClientInterface
{
    private const BASE_URL = 'https://us1.pdfgeneratorapi.com/api/v3';

    /** @var Client $client */
    protected $client;
    protected $key;
    protected $secret;
    protected $workspace;

    public function __construct(string $key, string $secret, string $workspace)
    {
        $this->key = $key;
        $this->secret = $secret;
        $this->workspace = $workspace;
    }

    public function createToken(int $TTLinSeconds = 300): string
    {
        $payload = [
            'iss' => $this->key,
            'sub' => $this->workspace,
            'exp' => time() + $TTLinSeconds
        ];
        return JWT::encode($payload, $this->secret, 'HS256');
    }
}
