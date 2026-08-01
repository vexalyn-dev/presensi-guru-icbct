<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ReportService;
use Illuminate\Http\Request;

class PerformanceReportController extends Controller
{
    public function index(Request $request, ReportService $service)
    {
        $month  = (int) $request->input('month', now()->month);
        $year   = (int) $request->input('year',  now()->year);
        $userId = $request->input('user_id') ?: null;

        $data   = $service->getPerformanceReport($month, $year, $userId ? (int) $userId : null);
        $users  = User::where('role', 'guru')->orderBy('name')->get(['id', 'name']);

        return view('reports.performance', [
            'performance' => $data['performance'],
            'chartData'   => $data['chart_data'],
            'users'       => $users,
            'month'       => $month,
            'year'        => $year,
            'userId'      => $userId,
        ]);
    }
}
