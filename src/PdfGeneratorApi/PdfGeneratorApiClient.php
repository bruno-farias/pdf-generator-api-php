<?php


namespace PdfGeneratorApi;


use Firebase\JWT\JWT;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use PdfGeneratorApi\Models\PdfGeneratorApiClientValidationException;
use PdfGeneratorApi\Models\PdfGeneratorApiException;
use PdfGeneratorApi\Models\Templates;

class PdfGeneratorApiClient implements ClientInterface
{
    private $client;
    private $key;
    private $secret;
    private $workspace;
    private const ALG = 'HS256';
    private const BASE_URL = 'https://us1.pdfgeneratorapi.com/api/v3';

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
                    'Authorization' => "Bearer $token"
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
            $uri = self::BASE_URL . "/templates/$templateId/output/?name=$name&format=$format&output=$output";
            $response = $this->getClient()->post($uri, [
                'json' => []
            ]);
            return json_decode($response->getBody(), true);
        } catch (ClientException $clientException) {
            throw new PdfGeneratorApiException($clientException->getMessage(), $clientException->getCode());
        }
    }

    public function validateFormat(string $format): void
    {
        if (!in_array($format, self::VALID_FORMATS)) {
            throw new PdfGeneratorApiClientValidationException('Invalid format');
        }
    }

    public function validateOutput(string $output): void
    {
        if (!in_array($output, self::VALID_OUTPUTS)) {
            throw new PdfGeneratorApiClientValidationException('Invalid output');
        }
    }
}
