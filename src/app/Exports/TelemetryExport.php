<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TelemetryExport implements FromCollection, WithHeadings
{
    protected $telemetry;

    public function __construct(Collection $telemetry)
    {
        $this->telemetry = $telemetry;
    }

    public function collection()
    {
        return $this->telemetry;
    }

    public function headings(): array
    {
        // Assuming your telemetry data has keys
        return array_keys($this->telemetry->first());
    }
}
