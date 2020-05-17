<?php


namespace Tests;


use Faker\Factory;
use Faker\Generator;

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
}
