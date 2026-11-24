<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Athlete;
use App\Models\District;
use App\Models\MedalHistory;
use App\Models\Sport;
use App\Models\Venue;
use App\Models\VenueRevenue;
use App\Models\VenueType;
use App\Models\VenueUsage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ImportController extends Controller
{
    /**
     * Validate CSV import file and generate preview with row-level error report.
     */
    public function validateImport(Request $request, string $dataset): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt',
        ]);

        $file = $request->file('file');
        $rows = $this->parseCsv($file->getRealPath());

        if (empty($rows)) {
            return response()->json([
                'message' => 'File CSV kosong atau tidak memiliki baris data.',
                'errors' => ['file' => ['File tidak berisi baris data yang valid.']],
            ], 422);
        }

        $headers = array_keys($rows[0]);
        $validationResult = $this->validateRows($dataset, $rows);

        return response()->json([
            'message' => 'Validasi CSV selesai.',
            'data' => [
                'dataset' => $dataset,
                'total_rows' => count($rows),
                'valid_count' => count($validationResult['valid_rows']),
                'error_count' => count($validationResult['errors']),
                'headers' => $headers,
                'preview' => array_slice($rows, 0, 10),
                'errors' => $validationResult['errors'],
                'valid_rows' => $validationResult['valid_rows'],
            ],
        ]);
    }

    /**
     * Commit validated rows into database.
     */
    public function commitImport(Request $request, string $dataset): JsonResponse
    {
        $validated = $request->validate([
            'rows' => 'required|array|min:1',
        ]);

        $rows = $validated['rows'];
        $validationResult = $this->validateRows($dataset, $rows);

        if (!empty($validationResult['errors'])) {
            return response()->json([
                'message' => 'Beberapa baris data gagal divalidasi dan tidak dapat diimpor.',
                'errors' => $validationResult['errors'],
            ], 422);
        }

        $insertedCount = 0;

        DB::beginTransaction();
        try {
            foreach ($validationResult['valid_rows'] as $row) {
                $this->insertRow($dataset, $row);
                $insertedCount++;
            }
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Terjadi kesalahan saat menyimpan data ke database: ' . $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'message' => "Berhasil mengimpor {$insertedCount} data {$dataset}.",
            'data' => [
                'dataset' => $dataset,
                'imported_count' => $insertedCount,
            ],
        ]);
    }

    protected function validateRows(string $dataset, array $rows): array
    {
        $validRows = [];
        $errors = [];

        foreach ($rows as $index => $row) {
            $rowNum = $index + 2; // +1 header, +1 1-indexed
            $rules = $this->getRulesForDataset($dataset);

            $validator = Validator::make($row, $rules);

            if ($validator->fails()) {
                $errors[] = [
                    'row' => $rowNum,
                    'data' => $row,
                    'messages' => $validator->errors()->all(),
                ];
            } else {
                $validRows[] = $validator->validated();
            }
        }

        return [
            'valid_rows' => $validRows,
            'errors' => $errors,
        ];
    }

    protected function getRulesForDataset(string $dataset): array
    {
        return match (strtolower($dataset)) {
            'venues' => [
                'name' => 'required|string|max:255',
                'venue_type_id' => 'required|integer|exists:venue_types,id',
                'district_id' => 'required|integer|exists:districts,id',
                'address' => 'required|string',
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
                'area_m2' => 'required|numeric|min:0',
                'capacity' => 'required|integer|min:0',
                'year_built' => 'required|integer',
                'manager' => 'nullable|string',
                'state' => 'nullable|in:draft,validated,published',
            ],
            'medals' => [
                'sport_id' => 'required|integer|exists:sports,id',
                'event' => 'required|string|max:255',
                'year' => 'required|integer|min:2000',
                'gold' => 'required|integer|min:0',
                'silver' => 'required|integer|min:0',
                'bronze' => 'required|integer|min:0',
                'state' => 'nullable|in:draft,validated,published',
            ],
            'revenue' => [
                'venue_id' => 'required|integer|exists:venues,id',
                'month' => 'required|date',
                'amount' => 'required|numeric|min:0',
            ],
            'usage' => [
                'venue_id' => 'required|integer|exists:venues,id',
                'month' => 'required|date',
                'visits' => 'required|integer|min:0',
            ],
            default => [
                'name' => 'required',
            ],
        };
    }

    protected function insertRow(string $dataset, array $row): void
    {
        match (strtolower($dataset)) {
            'venues' => Venue::create(array_merge([
                'state' => 'draft',
                'is_dummy' => false,
                'data_source' => 'CSV Import',
            ], $row)),
            'medals' => MedalHistory::create(array_merge([
                'state' => 'draft',
                'is_dummy' => false,
                'data_source' => 'CSV Import',
            ], $row)),
            'revenue' => VenueRevenue::updateOrCreate(
                ['venue_id' => $row['venue_id'], 'month' => $row['month']],
                ['amount' => $row['amount']]
            ),
            'usage' => VenueUsage::updateOrCreate(
                ['venue_id' => $row['venue_id'], 'month' => $row['month']],
                ['visits' => $row['visits']]
            ),
            default => null,
        };
    }

    protected function parseCsv(string $path): array
    {
        $handle = fopen($path, 'r');
        if (!$handle) {
            return [];
        }

        $header = null;
        $rows = [];

        while (($line = fgetcsv($handle, 1000, ',')) !== false) {
            if (!$header) {
                // Remove BOM if present
                $line[0] = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $line[0]);
                $header = array_map('trim', $line);
            } else {
                if (count($line) === count($header)) {
                    $rows[] = array_combine($header, array_map('trim', $line));
                }
            }
        }

        fclose($handle);
        return $rows;
    }
}
