<?php


namespace tests\Unit\Client;


use Firebase\JWT\JWT;
use PdfGeneratorApi\PdfGeneratorApiClient;
use PHPUnit\Framework\TestCase;
use Tests\TestHelper;

class AuthenticationTest extends TestCase
{
    use TestHelper;

    public function testCreatesValidToken()
    {
        $key = $this->generateRandomHash();
        $secret = $this->generateRandomHash();
        $workspace = $this-> generateCompanyEmail();
        $client = new PdfGeneratorApiClient($key, $secret, $workspace);

        $token = $client->createToken();
        $decodedToken = JWT::decode($token, $secret, ['HS256']);

        $this->assertEquals($workspace, $decodedToken->sub);
        $this->assertEquals($key, $decodedToken->iss);
    }
}
