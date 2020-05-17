<?php


namespace tests\Integration\Client;


use PdfGeneratorApi\Models\PdfGeneratorApiException;
use PdfGeneratorApi\Models\Template;
use PdfGeneratorApi\PdfGeneratorApiClient;
use PHPUnit\Framework\TestCase;
use Tests\TestHelper;

class ClientIntegrationTest extends TestCase
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
            $this->validateTemplateContent($source, $template);
        }
    }

    /**
     * @dataProvider mergeTemplateProvider
     * @param string $templateName
     * @param int $templateId
     * @param string $data
     * @param string $encoding
     * @param string $contentType
     * @param string $output
     * @throws PdfGeneratorApiException
     */
    public function testMergeTemplatesSucceedsWithBase64(
        string $templateName,
        int $templateId,
        string $data,
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
                'content-type' => $contentType
            ]
        ];
        $client = $this->mockResponse(200, $response);
        $this->pdfGeneratorApiClient->setClient($client);
        $mergeResponse = $this->pdfGeneratorApiClient->mergeTemplate($templateId, $templateName, $data, $encoding, $output);
        $this->assertEquals($baseResponse, $mergeResponse->getResponse());
        $this->assertEquals("$templateName.pdf", $mergeResponse->getName());
        $this->assertEquals($templateName, $mergeResponse->getDisplayName());
        $this->assertEquals($encoding, $mergeResponse->getEncoding());
        $this->assertEquals($contentType, $mergeResponse->getContentType());
    }

    public function mergeTemplateProvider(): array
    {
        return [
            ['tpl_pdf_b64', 1, '', 'pdf', 'application/pdf', 'base64'],
            ['tpl_pdf_url', 2, '', 'pdf', 'application/pdf', 'url'],
            ['tpl_pdf_I', 3, '', 'pdf', 'application/pdf', 'I'],

            ['tpl_html_b64', 4, '', 'html', 'text/html', 'base64'],
            ['tpl_html_url', 5, '', 'html', 'text/html', 'url'],
            ['tpl_html_I', 6, '', 'html', 'text/html', 'I'],

            ['tpl_zip_b64', 7, '', 'zip', 'application/zip', 'base64'],
            ['tpl_zip_url', 8, '', 'zip', 'application/zip', 'url'],
            ['tpl_zip_I', 9, '', 'zip', 'application/zip', 'I'],

            ['tpl_xlsx_b64', 10, '', 'xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'base64'],
            ['tpl_xlsx_url', 11, '', 'xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'url'],
            ['tpl_xlsx_I', 12, '', 'xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'I'],
        ];
    }
}
