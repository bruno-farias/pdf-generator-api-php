<?php


namespace PdfGeneratorApi;


use GuzzleHttp\Client;
use PdfGeneratorApi\Models\Templates;

interface ClientInterface
{
    const VALID_FORMATS = ['pdf', 'html', 'zip', 'xlsx'];
    const VALID_OUTPUTS = ['base64', 'url', 'I'];

    public function createToken(int $TTLinSeconds = 300): string;

    public function getClient(): Client;

    public function setClient(Client $client): void;

    public function getTemplates(): Templates;

    public function mergeTemplate(int $templateId, string $name, string $format = 'pdf', string $output = 'base64');

}
