<?php


namespace Tests;


use Faker\Factory;
use Faker\Generator;

trait TestHelper
{
    public function dd($variable)
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

    private function faker(): Generator
    {
        return Factory::create();
    }

    private function generateId(): int
    {
        return $this->faker()->numberBetween(10000, 99999);
    }

    private function generateName(): string
    {
        return $this->faker()->name;
    }

    private function generateDateTime()
    {
        return $this->faker()->dateTimeBetween('-3 years')->format('Y-m-d H:m:s');
    }

    private function randomBool(): bool
    {
        return $this->faker()->boolean;
    }

    private function generateTags(): array
    {
        $options = [
            $this->generateName(),
            $this->generateName(),
            $this->generateName(),
        ];
        $count = $this->faker()->numberBetween(0, 3);
        return $this->faker()->randomElements($options, $count);
    }
}
