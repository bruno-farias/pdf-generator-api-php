<?php


namespace PdfGeneratorApi\Models;


use PdfGeneratorApi\ArrayOrJson;

class TemplateMergeResponse
{
    use ArrayOrJson;

    private $response;
    private $name;
    private $displayName;
    private $encoding;
    private $contentType;

    public function __construct(array $response)
    {
        $this->response = $response['response'];
        $this->name = $response['meta']['name'];
        $this->displayName = $response['meta']['display_name'];
        $this->encoding = $response['meta']['encoding'];
        $this->contentType = $response['meta']['content-type'];
    }

    public function getResponse(): string
    {
        return $this->response;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDisplayName(): string
    {
        return $this->displayName;
    }

    public function getEncoding(): string
    {
        return $this->encoding;
    }

    public function getContentType(): string
    {
        return $this->contentType;
    }
}
