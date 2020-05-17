<?php


namespace PdfGeneratorApi\Models;


use PdfGeneratorApi\ArrayOrJson;

class Templates
{
    use ArrayOrJson;

    private $templates;

    public function __construct(array $items)
    {
        $this->templates = [];
        $this->set($items);
    }

    public function getTemplates(): array
    {
        return $this->templates;
    }

    private function set(array $items): void
    {
        foreach ($items as $item) {
            array_push($this->templates, new Template($item));
        }
    }
}
