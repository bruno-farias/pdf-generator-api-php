<?php


namespace tests;


use Faker\Factory;

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
        return Factory::create()->companyEmail;
    }
}
