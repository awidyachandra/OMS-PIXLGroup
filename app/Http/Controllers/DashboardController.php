<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Unit;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function dbstorage()
    {
        $totalUnits = Unit::where('is_backup', 0)->count();

        $available = Unit::where('is_backup', 0)
            ->where('status', 'available')
            ->count();

        $rented = Unit::where('is_backup', 0)
            ->where('status', 'rented')
            ->count();

        $maintenance = Unit::where('is_backup', 0)
            ->where('status', 'maintenance')
            ->count();

        $productTypes = Unit::select('kategori')
            ->where('is_backup', 0)
            ->whereNotNull('kategori')
            ->distinct()
            ->orderBy('kategori')
            ->pluck('kategori');

        $categoryStocks = Unit::select('kategori')
            ->selectRaw('COUNT(*) as total')
            ->where('is_backup', 0)
            ->whereNotNull('kategori')
            ->groupBy('kategori')
            ->orderBy('kategori')
            ->get();

        $quickAssign = Order::where('status', 'processed')
            ->with('details')
            ->latest()
            ->take(2)
            ->get();

        $upcoming = Order::whereIn('status', ['processed', 'assigned'])
            ->with('details')
            ->orderBy('date')
            ->take(5)
            ->get();

        $rentedOrders = Order::where('status', 'on rent')
            ->with('details')
            ->orderBy('return_date')
            ->take(5)
            ->get();

        $overdue = Order::where('status', 'on rent')
            ->with('details')
            ->whereDate('return_date', '<', now())
            ->get();

        return view('storage.dashboard', compact(
            'totalUnits',
            'available',
            'rented',
            'maintenance',
            'productTypes',
            'categoryStocks',
            'quickAssign',
            'upcoming',
            'rentedOrders',
            'overdue'
        ))->with([
            'availableStock' => null,
            'pickupDate' => null,
            'returnDate' => null,
            'productType' => null,
            'totalStock' => null,
            'usedStock' => null,
        ]);
    }

    public function dbmarketing(Request $request)
    {
        $totalUnits = Unit::where('is_backup', 0)->count();

        $available = Unit::where('is_backup', 0)
            ->where('status', 'available')
            ->count();

        $rented = Unit::where('is_backup', 0)
            ->where('status', 'rented')
            ->count();

        $maintenance = Unit::where('is_backup', 0)
            ->where('status', 'maintenance')
            ->count();

        $productTypes = Unit::select('kategori')
            ->where('is_backup', 0)
            ->whereNotNull('kategori')
            ->distinct()
            ->orderBy('kategori')
            ->pluck('kategori');

        $categoryStocks = Unit::select('kategori')
            ->selectRaw('COUNT(*) as total')
            ->where('is_backup', 0)
            ->whereNotNull('kategori')
            ->groupBy('kategori')
            ->orderBy('kategori')
            ->get();

        $totalOrders = Order::count();
        $trendYear = $request->get('year', now()->year);
$trendMonth = $request->get('month');

$query = Order::query()
    ->whereNotNull('date')
    ->whereYear('date', $trendYear);

if (!empty($trendMonth)) {
    $query->whereMonth('date', $trendMonth);
}

$monthlyOrders = $query
    ->selectRaw('MONTH(date) as month, COUNT(*) as total')
    ->groupByRaw('MONTH(date)')
    ->pluck('total', 'month')
    ->toArray();

$orderTrend = [];

for ($i = 1; $i <= 12; $i++) {
    $orderTrend[] = $monthlyOrders[$i] ?? 0;
}

$availableYears = Order::whereNotNull('date')
    ->selectRaw('YEAR(date) as year')
    ->distinct()
    ->orderByDesc('year')
    ->pluck('year');

        $pendingApproval = Order::where('status', 'pending approval')->count();
        $processedOrders = Order::where('status', 'processed')->count();
        $assignedOrders = Order::where('status', 'assigned')->count();
        $onRentOrders = Order::where('status', 'on rent')->count();
        $returnCheckingOrders = Order::where('status', 'return checking')->count();
        $completed = Order::where('status', 'completed')->count();
        $cancelledOrders = Order::where('status', 'cancelled')->count();

        $pipelineData = [
            'pending_approval' => $pendingApproval,
            'processed' => $processedOrders,
            'assigned' => $assignedOrders,
            'on_rent' => $onRentOrders,
            'return_checking' => $returnCheckingOrders,
            'completed' => $completed,
            'cancelled' => $cancelledOrders,
        ];

        // =========================
        // ORDER TREND PER BULAN
        // =========================
        $monthlyOrders = Order::selectRaw('MONTH(date) as month, COUNT(*) as total')
            ->whereNotNull('date')
            ->whereYear('date', now()->year)
            ->groupByRaw('MONTH(date)')
            ->pluck('total', 'month')
            ->toArray();

        $orderTrend = [];

        for ($i = 1; $i <= 12; $i++) {
            $orderTrend[] = $monthlyOrders[$i] ?? 0;
        }

        return view('marketing.dashboard', compact(
            'totalUnits',
            'available',
            'rented',
            'maintenance',
            'productTypes',
            'categoryStocks',
            'totalOrders',
            'pendingApproval',
            'dpPaid',
            'processedOrders',
            'assignedOrders',
            'fullyPaid',
            'onRentOrders',
            'returnCheckingOrders',
            'completed',
            'cancelledOrders',
            'pipelineData',
            'availableYears',
'trendYear',
'trendMonth',
            'orderTrend'
        ))->with([
            'availableStock' => null,
            'pickupDate' => null,
            'returnDate' => null,
            'productType' => null,
            'totalStock' => null,
            'usedStock' => null,
        ]);
    }

    public function dbowner(Request $request)
    {
        $totalOrders = Order::count();
        $trendYear = $request->get('year', now()->year);
$trendMonth = $request->get('month');

$query = Order::query()
    ->whereNotNull('date')
    ->whereYear('date', $trendYear);

if (!empty($trendMonth)) {
    $query->whereMonth('date', $trendMonth);
}

$monthlyOrders = $query
    ->selectRaw('MONTH(date) as month, COUNT(*) as total')
    ->groupByRaw('MONTH(date)')
    ->pluck('total', 'month')
    ->toArray();

$orderTrend = [];

for ($i = 1; $i <= 12; $i++) {
    $orderTrend[] = $monthlyOrders[$i] ?? 0;
}

$availableYears = Order::whereNotNull('date')
    ->selectRaw('YEAR(date) as year')
    ->distinct()
    ->orderByDesc('year')
    ->pluck('year');
        $pendingApproval = Order::where('status', 'pending approval')->count();
        $processedOrders = Order::where('status', 'processed')->count();
        $assignedOrders = Order::where('status', 'assigned')->count();
        $onRentOrders = Order::where('status', 'on rent')->count();
        $returnCheckingOrders = Order::where('status', 'return checking')->count();
        $completed = Order::where('status', 'completed')->count();
        $cancelledOrders = Order::where('status', 'cancelled')->count();

        $pipelineData = [
            'pending_approval' => $pendingApproval,
            'processed' => $processedOrders,
            'assigned' => $assignedOrders,
            'on_rent' => $onRentOrders,
            'return_checking' => $returnCheckingOrders,
            'completed' => $completed,
            'cancelled' => $cancelledOrders,
        ];

        for ($i = 1; $i <= 12; $i++) {
            $orderTrend[] = $monthlyOrders[$i] ?? 0;
        }

        $picOrders = Order::selectRaw('processed_by as pic, COUNT(*) as total_orders')
            ->whereNotNull('processed_by')
            ->where('processed_by', '!=', '')
            ->groupBy('processed_by')
            ->orderByDesc('total_orders')
            ->get();

        $totalPic = $picOrders->count();

        $totalUnits = Unit::where('is_backup', 0)->count();

        $availableUnits = Unit::where('is_backup', 0)
            ->where('status', 'available')
            ->count();

        $rentedUnits = Unit::where('is_backup', 0)
            ->where('status', 'rented')
            ->count();

        $maintenanceUnits = Unit::where('is_backup', 0)
            ->where('status', 'maintenance')
            ->count();

        $categoryStocks = Unit::select('kategori')
            ->selectRaw('COUNT(*) as total')
            ->selectRaw("SUM(CASE WHEN status = 'available' THEN 1 ELSE 0 END) as available")
            ->selectRaw("SUM(CASE WHEN status = 'rented' THEN 1 ELSE 0 END) as rented")
            ->selectRaw("SUM(CASE WHEN status = 'maintenance' THEN 1 ELSE 0 END) as maintenance")
            ->where('is_backup', 0)
            ->whereNotNull('kategori')
            ->groupBy('kategori')
            ->orderBy('kategori')
            ->get();

        $totalCustomers = Customer::count();

        $rawSegments = Customer::selectRaw('LOWER(TRIM(organization)) as segment, COUNT(*) as total')
            ->whereNotNull('organization')
            ->groupBy('segment')
            ->pluck('total', 'segment')
            ->toArray();

        $customerSegments = [
            'Umum' => (int) ($rawSegments['umum'] ?? 0),

            'Event Organizer' => (int) (
                ($rawSegments['event organizer'] ?? 0) +
                ($rawSegments['even organizer'] ?? 0)
            ),

            'Wedding Organizer' => (int) ($rawSegments['wedding organizer'] ?? 0),

            'BEM Fakultas' => (int) ($rawSegments['bem fakultas'] ?? 0),

            'BEM Universitas' => (int) ($rawSegments['bem universitas'] ?? 0),

            'HIMA Jurusan' => (int) (
                ($rawSegments['hima jurusan'] ?? 0) +
                ($rawSegments['himpunan mahasiswa'] ?? 0) +
                ($rawSegments['himpunan mahasiswa jurusan'] ?? 0)
            ),

            'OSIS' => (int) ($rawSegments['osis'] ?? 0),
        ];

        return view('owner.dashboard', compact(
            'totalOrders',
            'pendingApproval',
            'processedOrders',
            'assignedOrders',
            'onRentOrders',
            'returnCheckingOrders',
            'completed',
            'cancelledOrders',
            'pipelineData',
            'orderTrend',
'availableYears',
'trendYear',
'trendMonth',
            'picOrders',
            'totalPic',

            'totalUnits',
            'availableUnits',
            'rentedUnits',
            'maintenanceUnits',
            'categoryStocks',

            'totalCustomers',
            'customerSegments'
        ));
    }
}