<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\LiveMonitoringService;

class LiveMonitoringController extends Controller
{
    public function __construct(protected LiveMonitoringService $service) {}

    public function index()
    {
        $initialData = $this->service->getLiveData();
        return view('admin.live-monitoring.index', compact('initialData'));
    }

    public function refresh()
    {
        return response()->json($this->service->getLiveData());
    }
}
