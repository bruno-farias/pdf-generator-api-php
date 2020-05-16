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
        $key = 'ad54aaff89ffdfeff178bb8a8f359b29fcb20edb56250b9f584aa2cb0162ed4a';
        $secret = 'c00c18db6be22a6ffb5386f8503eecf98165a68410539e4693a08d7d995f5f47';
        $workspace = 'demo.example@actualreports.com';
        $client = new PdfGeneratorApiClient($key, $secret, $workspace);

        $token = $client->createToken();
        $this->dd($token);
        $decodedToken = JWT::decode($token, $secret, ['HS256']);

        $this->assertEquals($workspace, $decodedToken->sub);
        $this->assertEquals($key, $decodedToken->iss);
    }
}
