<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Unit;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AvailabilityController extends Controller
{
    private function dashboardData()
    {
        /*
        =========================================
        STOK UTAMA SAJA
        Backup unit tidak dihitung di summary utama
        =========================================
        */

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

        return compact(
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
        );
    }

    public function checkByRange(Request $request)
    {
        $request->validate([
            'pickup_date' => 'required|date',
            'return_date' => 'required|date|after_or_equal:pickup_date',
            'product_type' => 'required|string',
        ]);

        $pickupDate = Carbon::parse($request->pickup_date)->toDateString();
        $returnDate = Carbon::parse($request->return_date)->toDateString();
        $productType = $request->product_type;

        /*
        =========================================
        TOTAL STOCK UTAMA SAJA
        Backup unit tidak dihitung availability normal
        =========================================
        */

        $totalStock = Unit::where('kategori', $productType)
            ->where('is_backup', 0)
            ->count();

        $usedStock = Order::whereIn('status', [
                'processed',
                'dp paid',
                'assigned',
                'on rent',
                'return checking'
            ])
            ->whereHas('details', function ($q) use ($productType) {
                $q->where('product_type', $productType);
            })
            ->whereRaw('DATE_SUB(pickup_date, INTERVAL 1 DAY) <= ?', [$returnDate])
            ->whereRaw('DATE_ADD(return_date, INTERVAL 1 DAY) >= ?', [$pickupDate])
            ->with('details')
            ->get()
            ->sum(function ($order) use ($productType) {
                return $order->details
                    ->where('product_type', $productType)
                    ->sum('qty');
            });

        $availableStock = max(0, $totalStock - $usedStock);

        return view('storage.dashboard', array_merge($this->dashboardData(), [
            'pickupDate' => $pickupDate,
            'returnDate' => $returnDate,
            'productType' => $productType,
            'totalStock' => $totalStock,
            'usedStock' => $usedStock,
            'availableStock' => $availableStock,
        ]));
    }

    private function getMarketingDashboardData()
    {
        /*
        =========================================
        STOK UTAMA SAJA
        Backup unit tidak dihitung di summary utama
        =========================================
        */

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

        $pendingApproval = Order::where('status', 'pending approval')->count();
        $dpPaid = Order::where('status', 'dp paid')->count();
        $processedOrders = Order::where('status', 'processed')->count();
        $assignedOrders = Order::where('status', 'assigned')->count();
        $fullyPaid = Order::where('status', 'fully paid')->count();
        $onRentOrders = Order::where('status', 'on rent')->count();
        $returnCheckingOrders = Order::where('status', 'return checking')->count();
        $completed = Order::where('status', 'completed')->count();
        $cancelledOrders = Order::where('status', 'cancelled')->count();

        $pipelineData = [
            'pending_approval' => $pendingApproval,
            'dp_paid' => $dpPaid,
            'processed' => $processedOrders,
            'assigned' => $assignedOrders,
            'fully_paid' => $fullyPaid,
            'on_rent' => $onRentOrders,
            'return_checking' => $returnCheckingOrders,
            'completed' => $completed,
            'cancelled' => $cancelledOrders,
        ];

        $monthlyOrders = Order::selectRaw('MONTH(date) as month, COUNT(*) as total')
            ->whereYear('date', now()->year)
            ->groupByRaw('MONTH(date)')
            ->pluck('total', 'month')
            ->toArray();

        $orderTrend = [];

        for ($i = 1; $i <= 12; $i++) {
            $orderTrend[] = $monthlyOrders[$i] ?? 0;
        }

        return compact(
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
            'orderTrend'
        );
    }

    public function checkMarketingByRange(Request $request)
    {
        $request->validate([
            'pickup_date' => 'required|date',
            'return_date' => 'required|date|after_or_equal:pickup_date',
            'product_type' => 'required|string',
        ]);

        $pickupDate = Carbon::parse($request->pickup_date)->toDateString();
        $returnDate = Carbon::parse($request->return_date)->toDateString();
        $productType = $request->product_type;

        $totalStock = Unit::where('kategori', $productType)
            ->where('is_backup', 0)
            ->count();

        $usedStock = Order::whereIn('status', [
                'processed',
                'dp paid',
                'assigned',
                'on rent',
                'return checking'
            ])
            ->whereHas('details', function ($q) use ($productType) {
                $q->where('product_type', $productType);
            })
            ->whereRaw('DATE_SUB(pickup_date, INTERVAL 1 DAY) <= ?', [$returnDate])
            ->whereRaw('DATE_ADD(return_date, INTERVAL 1 DAY) >= ?', [$pickupDate])
            ->with('details')
            ->get()
            ->sum(function ($order) use ($productType) {
                return $order->details
                    ->where('product_type', $productType)
                    ->sum('qty');
            });

        $availableStock = max(0, $totalStock - $usedStock);

        return view('marketing.dashboard', array_merge($this->getMarketingDashboardData(), [
            'pickupDate' => $pickupDate,
            'returnDate' => $returnDate,
            'productType' => $productType,
            'totalStock' => $totalStock,
            'usedStock' => $usedStock,
            'availableStock' => $availableStock,
        ]));
    }
}