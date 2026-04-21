<?php

namespace App\Services;

use App\Models\Company;
use App\Models\ImportBatch;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CompanyImportService
{
    public function import(UploadedFile $file, string $sourceType, User $user): array
    {
        $rows = $this->parse($file, $sourceType);

        return DB::transaction(function () use ($file, $rows, $sourceType, $user): array {
            $replacedRows = Company::query()->sourceType($sourceType)->count();
            Company::query()->sourceType($sourceType)->delete();

            $batch = ImportBatch::query()->create([
                'source_type' => $sourceType,
                'original_filename' => $file->getClientOriginalName(),
                'imported_rows' => count($rows['records']),
                'skipped_rows' => $rows['skipped_rows'],
                'replaced_rows' => $replacedRows,
                'imported_by' => $user->id,
            ]);

            foreach ($rows['records'] as $record) {
                Company::query()->create([
                    ...$record,
                    'import_batch_id' => $batch->id,
                ]);
            }

            return [
                'batch' => $batch,
                'imported_rows' => count($rows['records']),
                'skipped_rows' => $rows['skipped_rows'],
                'replaced_rows' => $replacedRows,
            ];
        });
    }

    public function parse(UploadedFile|string $file, string $sourceType): array
    {
        $source = config("company_sources.{$sourceType}");

        if ($source === null) {
            throw new InvalidArgumentException("Unsupported source type [{$sourceType}].");
        }

        $path = $file instanceof UploadedFile ? $file->getRealPath() : $file;
        $reader = IOFactory::createReaderForFile($path);
        $reader->setReadDataOnly(true);

        /** @var Worksheet $sheet */
        $sheet = $reader->load($path)->getActiveSheet();
        $highestRow = $sheet->getHighestDataRow();
        $records = [];
        $skippedRows = 0;

        for ($row = $source['start_row']; $row <= $highestRow; $row++) {
            $companyName = trim((string) $sheet->getCell($source['company_column'].$row)->getFormattedValue());
            $email = trim((string) $sheet->getCell($source['email_column'].$row)->getFormattedValue());
            $incorporationDate = $this->parseDate(
                $sheet,
                $source['incorporation_date_column'],
                $row,
            );

            if ($companyName === '' && $email === '' && $incorporationDate === null) {
                continue;
            }

            if ($companyName === '' || $email === '' || $incorporationDate === null || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $skippedRows++;
                continue;
            }

            $records[] = [
                'source_type' => $sourceType,
                'company_name' => $companyName,
                'email' => $email,
                'incorporation_date' => $incorporationDate->toDateString(),
                'active' => true,
            ];
        }

        return [
            'records' => $records,
            'skipped_rows' => $skippedRows,
        ];
    }

    private function parseDate(Worksheet $sheet, string $column, int $row): ?Carbon
    {
        $cell = $sheet->getCell($column.$row);
        $value = $cell->getValue();

        if ($value === null || $value === '') {
            return null;
        }

        if (is_numeric($value)) {
            return Carbon::instance(ExcelDate::excelToDateTimeObject($value))->startOfDay();
        }

        if ($value instanceof \DateTimeInterface) {
            return Carbon::instance($value)->startOfDay();
        }

        try {
            return Carbon::parse(trim((string) $cell->getFormattedValue()))->startOfDay();
        } catch (\Throwable) {
            return null;
        }
    }
}
