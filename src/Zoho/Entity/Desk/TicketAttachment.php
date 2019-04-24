<?php

namespace App\Zoho\Entity\Desk;

class TicketAttachment
{
    /**
     * @var string
     */
    private $content;

    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    public function getContent(): string
    {
        return $this->content;
    }
}
