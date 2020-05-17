<?php


namespace tests\Unit\Models;


use PdfGeneratorApi\Models\TemplateMergeResponse;
use PHPUnit\Framework\TestCase;
use Tests\TestHelper;

class TemplateMergeResponseTest extends TestCase
{
    use TestHelper;

    public function testTemplateMergeResponseReturnsCorrectData()
    {
        $input = $this->generateTemplateMergeResponse();
        $templateMerge = new TemplateMergeResponse($input);

        $this->assertEquals($input['response'], $templateMerge->getResponse());
        $this->assertEquals($input['meta']['name'], $templateMerge->getName());
        $this->assertEquals($input['meta']['display_name'], $templateMerge->getDisplayName());
        $this->assertEquals($input['meta']['encoding'], $templateMerge->getEncoding());
        $this->assertEquals($input['meta']['content-type'], $templateMerge->getContentType());
    }
}
