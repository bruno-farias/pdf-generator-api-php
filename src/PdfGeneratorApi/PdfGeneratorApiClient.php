<?php


namespace PdfGeneratorApi;


use Firebase\JWT\JWT;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use PdfGeneratorApi\Models\ClientValidationException;
use PdfGeneratorApi\Models\PdfGeneratorApiException;
use PdfGeneratorApi\Models\Templates;

class PdfGeneratorApiClient implements ClientInterface
{
    const BASE_URL = 'https://us1.pdfgeneratorapi.com/api/v3';

    /** @var Client $client */
    protected $client;
    protected $key;
    protected $secret;
    protected $workspace;

    const ALG = 'HS256';

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
        return JWT::encode($payload, $this->secret, self::ALG);
    }

    public function getClient(): Client
    {
        if (!$this->client) {
            $token = $this->createToken();
            $this->client = new Client([
                'headers' => [
                    'Authorization' => "Bearer {$token}"
                ]
            ]);
        }
        return $this->client;
    }

    public function setClient(Client $client): void
    {
        $this->client = $client;
    }

    public function getTemplates(): Templates
    {
        try {
            $response = $this->getClient()->get(self::BASE_URL . "/templates");
            $data = json_decode($response->getBody(), true);
            return new Templates($data['response']);
        } catch (ClientException $clientException) {
            throw new PdfGeneratorApiException($clientException->getMessage(), $clientException->getCode());
        }
    }

    public function mergeTemplate(int $templateId, string $name, string $format = 'pdf', string $output = 'base64')
    {
        try {
            $this->validateFormat($format);
            $this->validateOutput($output);
            $uri = self::BASE_URL . "/templates/output/?name={$name}&format={$format}&output={$output}";
            $response = $this->getClient()->post($uri, [
                'json' => []
            ]);
        } catch (ClientException $clientException) {
            throw new PdfGeneratorApiException($clientException->getMessage(), $clientException->getCode());
        }
    }

    private function validateFormat(string $format): void
    {
        if (!in_array($format, self::VALID_FORMATS)) {
            throw new ClientValidationException("Invalid format");
        }
    }

    private function validateOutput(string $output): void
    {
        if (!in_array($output, self::VALID_OUTPUTS)) {
            throw new ClientValidationException("Invalid output");
        }
    }
}
