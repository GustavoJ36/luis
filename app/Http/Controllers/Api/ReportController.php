<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\ReportRepository;
use App\Http\Requests\Report\TopProductReportRequest;

class ReportController extends Controller
{
    private ReportRepository $reportRepository;

    public function __construct(ReportRepository $reportRepository)
    {
        $this->reportRepository = $reportRepository;
    }

    /**
     * Get top selling products within a date range.
     *
     * @param TopProductReportRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function topProducts(TopProductReportRequest $request)
    {

        $from = $request->input('from');
        $to = $request->input('to');
        $limit = $request->input('limit', 5);

        $topProducts = $this->reportRepository->getTopProducts($from, $to, $limit);

        return response()->json($topProducts);
    }
}
