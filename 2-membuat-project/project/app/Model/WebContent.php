<?php

namespace AriefKarditya\LocalDomainPhp\Model;

class WebContent
{
    public function __construct(
        private string $content,
        private string $title,
    ) {
        # constructor
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getTitle(): string
    {
        return $this->title;
    }
    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }
}
