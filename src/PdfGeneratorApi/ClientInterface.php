<?php


namespace PdfGeneratorApi;


use GuzzleHttp\Client;
use PdfGeneratorApi\Models\Templates;

interface ClientInterface
{
    public function createToken(int $TTLinSeconds = 300): string;

    public function getClient(): Client;

    public function setClient(Client $client): void;

    public function getTemplates(): Templates;

}
