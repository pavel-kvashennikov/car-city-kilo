<?php

declare(strict_types=1);

namespace Src\Parts\Application\UseCases\ImportProductsFromFile;

use Illuminate\Http\UploadedFile;

final class ImportProductsFromFileCommand
{
    public function __construct(
        public readonly int $partsProfileId,
        public readonly UploadedFile $file,
        public readonly string $format = 'xlsx',
    ) {}
}
