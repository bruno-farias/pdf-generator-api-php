<?php


namespace tests\Unit\Models;


use PdfGeneratorApi\Models\Template;
use PHPUnit\Framework\TestCase;
use Tests\TestHelper;

class TemplateModelTest extends TestCase
{
    use TestHelper;

    public function testTemplateModelReturnCorrectData()
    {
        $input = $this->generateTemplateResponse();
        $template = new Template($input);
        $this->validateTemplateContent($input, $template);
    }
}
