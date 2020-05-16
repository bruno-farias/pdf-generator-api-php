<?php


namespace tests\Unit\Client;


use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PdfGeneratorApi\Models\Template;
use PdfGeneratorApi\PdfGeneratorApiClient;
use PHPUnit\Framework\TestCase;
use Tests\TestHelper;

class TemplatesTest extends TestCase
{
    use TestHelper;

    public function testGetTemplates()
    {
        $response = [
            'response' => [
                $this->generateTemplateResponse(),
                $this->generateTemplateResponse(),
                $this->generateTemplateResponse(),
            ]
        ];

        $mock = new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], json_encode($response))
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $pdfGeneratorApiClient = new PdfGeneratorApiClient($this->generateRandomHash(), $this->generateRandomHash(),
            $this->generateCompanyEmail());
        $pdfGeneratorApiClient->setClient($client);
        $result = $pdfGeneratorApiClient->getTemplates();

        for ($item = 0; $item < count($result->getTemplates()); $item++) {
            /** @var Template $template */
            $template = $result->getTemplates()[$item];
            $source = $response['response'][$item];
            $this->assertEquals($source['id'], $template->getId());
            $this->assertEquals($source['name'], $template->getName());
            $this->assertEquals($source['modified'], $template->getModified());
            $this->assertEquals($source['owner'], $template->getOwner());
            $this->assertEquals($source['tags'], $template->getTags());
        }
    }
}
