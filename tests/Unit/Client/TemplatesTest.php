<?php


namespace tests\Unit\Client;


use PdfGeneratorApi\Models\PdfGeneratorApiClientValidationException;
use PdfGeneratorApi\Models\PdfGeneratorApiException;
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

    public function testMergeTemplatesThrowExceptionWithInvalidFormatParameter()
    {
        $this->expectException(PdfGeneratorApiClientValidationException::class);
        $this->expectExceptionMessage('Invalid format');
        $this->pdfGeneratorApiClient->mergeTemplate($this->generateId(), $this->generateName(), '',
            $this->generateName());
    }

    public function testMergeTemplatesThrowExceptionWithInvalidOutputParameter()
    {
        $this->expectException(PdfGeneratorApiClientValidationException::class);
        $this->expectExceptionMessage('Invalid output');
        $this->pdfGeneratorApiClient->mergeTemplate($this->generateId(), $this->generateName(), '',
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
        $this->pdfGeneratorApiClient->mergeTemplate($templateId, $this->generateName(), '');
    }
}
