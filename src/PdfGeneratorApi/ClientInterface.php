<?php


namespace PdfGeneratorApi;


interface ClientInterface
{
    public function createToken(int $TTLinSeconds = 300): string ;

}
