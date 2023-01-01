<?php

namespace App\Exports;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ExportReport implements FromView
{
    protected $records;
    protected $days;

    public function __construct(array $records, array $days)
    {
        $this->records = $records;
        $this->days = $days;
    }

    public function view(): View
    {
        return view('exports.report', ['records' => $this->records, 'days' => $this->days]);
    }
}
