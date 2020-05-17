<?php


namespace tests\Unit\Client;


use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PdfGeneratorApi\Models\PdfGeneratorApiClientValidationException;
use PdfGeneratorApi\Models\PdfGeneratorApiException;
use PdfGeneratorApi\Models\Template;
use PdfGeneratorApi\PdfGeneratorApiClient;
use PHPUnit\Framework\TestCase;
use Tests\TestHelper;

class TemplatesTest extends TestCase
{
    use TestHelper;

    private $pdfGeneratorApiClient;

    public function setUp(): void
    {
        parent::setUp();
        $this->pdfGeneratorApiClient = new PdfGeneratorApiClient(
            $this->generateRandomHash(),
            $this->generateRandomHash(),
            $this->generateCompanyEmail()
        );
    }

    public function testGetTemplates()
    {
        $response = [
            'response' => [
                $this->generateTemplateResponse(),
                $this->generateTemplateResponse(),
                $this->generateTemplateResponse(),
            ]
        ];
        $client = $this->mockResponse(200, $response);
        $this->pdfGeneratorApiClient->setClient($client);
        $result = $this->pdfGeneratorApiClient->getTemplates();

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

    public function testMergeTemplatesThrowExceptionWithInvalidFormatParameter()
    {
        $this->expectException(PdfGeneratorApiClientValidationException::class);
        $this->expectExceptionMessage('Invalid format');
        $this->pdfGeneratorApiClient->mergeTemplate($this->generateId(), $this->generateName(), $this->generateName());
    }

    public function testMergeTemplatesThrowExceptionWithInvalidOutputParameter()
    {
        $this->expectException(PdfGeneratorApiClientValidationException::class);
        $this->expectExceptionMessage('Invalid output');
        $this->pdfGeneratorApiClient->mergeTemplate($this->generateId(), $this->generateName(),
            $this->getValidFormats(), $this->generateName());
    }

    public function testMergeTemplatesThrowExceptionWithMissingParametersIncorrect()
    {
        $templateId = $this->generateId(9999999, 999999999999);
        $status = 404;
        $response = [
            'error' => "Entity not found: Template with id $templateId not found",
            'status' => $status
        ];
        $client = $this->mockResponse($status, $response);
        $this->pdfGeneratorApiClient->setClient($client);
        $this->expectException(PdfGeneratorApiException::class);
        $this->expectExceptionMessage("Entity not found: Template with id $templateId not found");
        $this->expectExceptionCode($status);
        $this->pdfGeneratorApiClient->mergeTemplate($templateId, $this->generateName());
    }

    /**
     * @dataProvider mergeTemplateProvider
     * @param string $templateName
     * @param int $templateId
     * @param string $encoding
     * @param string $contentType
     * @param string $output
     * @throws PdfGeneratorApiException
     */
    public function testMergeTemplatesSucceedsWithBase64(
        string $templateName,
        int $templateId,
        string $encoding,
        string $contentType,
        string $output
    ) {
        $baseResponse = $this->generateBase64();
        $response = [
            'response' => $baseResponse,
            'meta' => [
                'name' => "$templateName.pdf",
                'display_name' => $templateName,
                'encoding' => $encoding,
                'content-Type' => $contentType
            ]
        ];
        $client = $this->mockResponse(200, $response);
        $this->pdfGeneratorApiClient->setClient($client);
        $result = $this->pdfGeneratorApiClient->mergeTemplate($templateId, $templateName, $encoding, $output);
        $this->assertEquals($baseResponse, $result['response']);
        $this->assertEquals("$templateName.pdf", $result['meta']['name']);
        $this->assertEquals($templateName, $result['meta']['display_name']);
        $this->assertEquals($encoding, $result['meta']['encoding']);
        $this->assertEquals($contentType, $result['meta']['content-Type']);
    }

    public function mergeTemplateProvider(): array
    {
        return [
            ['tpl_pdf_b64', 1, 'pdf', 'application/pdf', 'base64'],
            ['tpl_pdf_url', 2, 'pdf', 'application/pdf', 'url'],
            ['tpl_pdf_I', 3, 'pdf', 'application/pdf', 'I'],

            ['tpl_html_b64', 4, 'html', 'text/html', 'base64'],
            ['tpl_html_url', 5, 'html', 'text/html', 'url'],
            ['tpl_html_I', 6, 'html', 'text/html', 'I'],

            ['tpl_zip_b64', 7, 'zip', 'application/zip', 'base64'],
            ['tpl_zip_url', 8, 'zip', 'application/zip', 'url'],
            ['tpl_zip_I', 9, 'zip', 'application/zip', 'I'],

            ['tpl_xlsx_b64', 10, 'xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'base64'],
            ['tpl_xlsx_url', 11, 'xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'url'],
            ['tpl_xlsx_I', 12, 'xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'I'],
        ];
    }

    private function mockResponse(int $status, array $response): Client
    {
        $mock = new MockHandler([
            new Response($status, ['Content-Type' => 'application/json'], json_encode($response))
        ]);
        $handlerStack = HandlerStack::create($mock);
        return new Client(['handler' => $handlerStack]);
    }
}
