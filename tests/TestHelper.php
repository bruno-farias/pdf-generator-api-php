<?php


namespace tests;


use Faker\Factory;
use Faker\Generator;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PdfGeneratorApi\Models\Template;

trait TestHelper
{
    public function dd($variable): void
    {
        echo "";
        var_dump($variable);
        echo "";
        die;
    }

    public function generateRandomHash(): string
    {
        return bin2hex(random_bytes(64));
    }

    public function generateCompanyEmail(): string
    {
        return $this->faker()->companyEmail;
    }

    public function generateTemplateResponse(): array
    {
        return [
            'id' => $this->generateId(),
            'name' => $this->generateName(),
            'modified' => $this->generateDateTime(),
            'owner' => $this->randomBool(),
            'tags' => $this->generateTags()
        ];
    }

    public function generateTemplateMergeResponse(): array
    {
        $name = $this->generateName();
        $format = $this->getValidFormats();
        return [
            'response' => $this->generateBase64(),
            'meta' => [
                'name' => "$name.pdf",
                'display_name' => $name,
                'encoding' => 'base64',
                'content-type' => $this->getContentType($format)
            ]
        ];
    }

    public function faker(): Generator
    {
        return Factory::create();
    }

    public function generateId(int $min = 10000, int $max = 99999): int
    {
        return $this->faker()->numberBetween($min, $max);
    }

    public function generateName(): string
    {
        return $this->faker()->name;
    }

    public function generateDateTime()
    {
        return $this->faker()->dateTimeBetween('-3 years')->format('Y-m-d H:m:s');
    }

    public function randomBool(): bool
    {
        return $this->faker()->boolean;
    }

    public function generateTags(): array
    {
        $options = [
            $this->generateName(),
            $this->generateName(),
            $this->generateName(),
        ];
        $count = $this->faker()->numberBetween(0, 3);
        return $this->faker()->randomElements($options, $count);
    }

    public function getValidFormats(): string
    {
        return $this->faker()->randomElement(['pdf', 'html', 'zip', 'xlsx']);
    }

    public function getContentType(string $format)
    {
        switch ($format) {
            case 'pdf':
                return 'application/pdf';
            case 'html':
                return 'text/html';
            case 'zip':
                return 'application/zip';
            case 'xlsx':
                return 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
            default:
                return new \Exception('MIME Type not available!');
        }
    }

    public function getValidOutputs()
    {
        return $this->faker()->randomElement(['base64', 'url', 'I']);
    }

    public function generateBase64()
    {
        return base64_encode($this->generateRandomHash());
    }

    public function validateTemplateContent(array $source, Template $template)
    {
        $this->assertEquals($source['id'], $template->getId());
        $this->assertEquals($source['name'], $template->getName());
        $this->assertEquals($source['modified'], $template->getModified());
        $this->assertEquals($source['owner'], $template->getOwner());
        $this->assertEquals($source['tags'], $template->getTags());
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
