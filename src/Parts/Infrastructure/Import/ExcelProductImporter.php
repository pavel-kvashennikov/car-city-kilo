<?php

declare(strict_types=1);

namespace Src\Parts\Infrastructure\Import;

use Illuminate\Http\UploadedFile;
use Maatwebsite\Excel\Facades\Excel;
use Src\Parts\Domain\Models\Product;

class ExcelProductImporter
{
    public function import(UploadedFile $file, int $partsProfileId): array
    {
        $rows = Excel::toArray(null, $file);
        $imported = 0;
        $errors = [];

        if (empty($rows) || empty($rows[0])) {
            return ['imported' => 0, 'errors' => ['Empty file']];
        }

        $data = $rows[0];
        $headers = array_shift($data);

        foreach ($data as $index => $row) {
            try {
                $mapped = $this->mapRow($headers, $row);

                Product::updateOrCreate(
                    [
                        'parts_profile_id' => $partsProfileId,
                        'article_number' => $mapped['article_number'] ?? null,
                    ],
                    array_merge($mapped, [
                        'parts_profile_id' => $partsProfileId,
                        'status' => 'active',
                    ])
                );

                $imported++;
            } catch (\Throwable $e) {
                $errors[] = 'Row '.($index + 2).': '.$e->getMessage();
            }
        }

        return [
            'imported' => $imported,
            'errors' => $errors,
        ];
    }

    private function mapRow(array $headers, array $row): array
    {
        $mapped = [];
        $headerMap = [
            'название' => 'name',
            'name' => 'name',
            'артикул' => 'article_number',
            'article' => 'article_number',
            'oem' => 'oem_number',
            'бренд' => 'brand',
            'brand' => 'brand',
            'цена' => 'price_retail',
            'price' => 'price_retail',
            'количество' => 'quantity',
            'quantity' => 'quantity',
            'описание' => 'description',
            'description' => 'description',
        ];

        foreach ($headers as $index => $header) {
            $normalizedHeader = mb_strtolower(trim($header));
            if (isset($headerMap[$normalizedHeader]) && isset($row[$index])) {
                $mapped[$headerMap[$normalizedHeader]] = $row[$index];
            }
        }

        return $mapped;
    }
}
