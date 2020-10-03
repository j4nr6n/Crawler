<?php

namespace App\Message;

class Crawl extends AsyncMessage
{
    private array $urlParts;

    public function __construct(array $urlParts)
    {
        $this->urlParts = $urlParts;
    }

    public function getUrlParts(): array
    {
        return $this->urlParts;
    }
}
