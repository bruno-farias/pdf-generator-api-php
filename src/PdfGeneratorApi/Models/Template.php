<?php


namespace PdfGeneratorApi\Models;


use PdfGeneratorApi\ArrayOrJson;

class Template
{
    use ArrayOrJson;

    private $id;
    private $name;
    private $owner;
    private $modified;
    private $tags;

    public function __construct(array $data)
    {
        $this->set($data);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getOwner(): bool
    {
        return $this->owner;
    }

    public function getModified()
    {
        return $this->modified;
    }

    public function getTags(): array
    {
        return $this->tags;
    }

    public function set(array $data): void
    {
        foreach ($data as $key => $value) {
            $this->{$key} = $value;
        }
    }
}
