<?php

declare(strict_types=1);

namespace Src\Parts\Application\UseCases\ImportProductsFromFile;

use Src\Parts\Infrastructure\Import\ExcelProductImporter;

final class ImportProductsFromFileHandler
{
    public function __construct(
        private readonly ExcelProductImporter $importer
    ) {}

    public function handle(ImportProductsFromFileCommand $command): array
    {
        return $this->importer->import(
            $command->file,
            $command->partsProfileId
        );
    }
}
