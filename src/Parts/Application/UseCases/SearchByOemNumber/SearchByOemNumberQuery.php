<?php

declare(strict_types=1);

namespace Src\Parts\Application\UseCases\SearchByOemNumber;

final class SearchByOemNumberQuery
{
    public function __construct(
        public readonly string $oemNumber,
        public readonly int $limit = 50,
    ) {}
}
