<?php


namespace PdfGeneratorApi;


trait ArrayOrJson
{
    public function asArray() : array
    {
        return get_object_vars($this);
    }

    public function asJson() : string
    {
        return json_encode($this->asArray());
    }
}
