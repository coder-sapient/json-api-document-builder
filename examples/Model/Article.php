<?php

declare(strict_types=1);

namespace CoderSapient\JsonApi\Examples\Model;

final class Article
{
    public function __construct(
        private string $id,
        private string $authorId,
        private string $title,
    ) {
    }

    public function id(): string
    {
        return $this->id;
    }

    public function authorId(): string
    {
        return $this->authorId;
    }

    public function title(): string
    {
        return $this->title;
    }
}
