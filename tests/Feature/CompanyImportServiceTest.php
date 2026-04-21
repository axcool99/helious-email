<?php

namespace Tests\Feature;

use App\Services\CompanyImportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompanyImportServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_parses_the_ar_source_file(): void
    {
        $service = app(CompanyImportService::class);
        $result = $service->parse(base_path('test data/AR test data.xlsx'), 'stacia');

        $this->assertNotEmpty($result['records']);
        $this->assertSame(1, $result['skipped_rows']);
        $this->assertSame('AUNTEA F&B (BSD) SDN BHD', $result['records'][0]['company_name']);
        $this->assertSame('2024-11-12', $result['records'][0]['incorporation_date']);
        $this->assertSame('hello@heliousthrive.com', $result['records'][0]['email']);
    }

    public function test_it_parses_the_violet_source_file(): void
    {
        $service = app(CompanyImportService::class);
        $result = $service->parse(base_path('test data/Violet - test data.xlsx'), 'violet');

        $this->assertNotEmpty($result['records']);
        $this->assertSame('EXPO & PRINT SDN BHD', $result['records'][0]['company_name']);
        $this->assertSame('2024-04-01', $result['records'][0]['incorporation_date']);
        $this->assertSame('hello@heliousthrive.com', $result['records'][0]['email']);
    }
}
