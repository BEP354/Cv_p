<?php

namespace App\Http\Controllers;

use App\Models\AdminAccount;
use App\Models\ConversionOrder;
use App\Models\PurchaseOrder;
use App\Models\User;
use App\Models\PaymentMethod;
use App\Models\ExchangeRate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!auth()->user() || !auth()->user()->isAdmin()) {
                abort(403, 'Akses ditolak. Anda tidak memiliki hak akses admin.');
            }
            return $next($request);
        });
    }

    // ===== BALANCE ORDERS (PURCHASE ORDERS) =====
    public function balanceOrders(Request $request)
    {
        try {
            $query = PurchaseOrder::with(['user', 'paymentMethod']);

            // Apply status filter
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            // Apply currency filter
            if ($request->filled('currency')) {
                $query->where('to_currency', $request->currency);
            }

            // Apply date range filter
            if ($request->filled('start_date')) {
                $query->whereDate('created_at', '>=', $request->start_date);
            }

            if ($request->filled('end_date')) {
                $query->whereDate('created_at', '<=', $request->end_date);
            }

            // Apply search filter
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('order_code', 'like', "%{$search}%")
                      ->orWhereHas('user', function($userQuery) use ($search) {
                          $userQuery->where('name', 'like', "%{$search}%")
                                   ->orWhere('email', 'like', "%{$search}%");
                      });
                });
            }

            // Apply amount range filter
            if ($request->filled('min_amount')) {
                $query->where('to_amount', '>=', $request->min_amount);
            }

            if ($request->filled('max_amount')) {
                $query->where('to_amount', '<=', $request->max_amount);
            }

            // Apply sorting
            $sortBy = $request->get('sort', 'newest');
            switch ($sortBy) {
                case 'oldest':
                    $query->oldest();
                    break;
                case 'amount_high':
                    $query->orderBy('total_idr', 'desc');
                    break;
                case 'amount_low':
                    $query->orderBy('total_idr', 'asc');
                    break;
                default:
                    $query->latest();
                    break;
            }

            // Handle export
            if ($request->get('export') === 'excel') {
                return $this->exportBalanceOrders($query);
            }

            // Pagination
            $perPage = $request->get('per_page', 10);
            $balanceOrders = $query->paginate($perPage);
            
            // Calculate stats
            $totalOrders = PurchaseOrder::count();
            $pendingOrders = PurchaseOrder::where('status', 'pending')->count();
            $completedOrders = PurchaseOrder::where('status', 'success')->count();
            $totalVolume = PurchaseOrder::where('status', 'success')->sum('total_idr') ?? 0;

            return view('admin.balance-orders', compact(
                'balanceOrders', 
                'totalOrders', 
                'pendingOrders', 
                'completedOrders', 
                'totalVolume'
            ));
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error loading balance orders: ' . $e->getMessage()]);
        }
    }

    private function exportBalanceOrders($query)
    {
        try {
            $orders = $query->get();
            
            $filename = 'balance_orders_' . date('Y-m-d_H-i-s') . '.csv';
            
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];

            $callback = function() use ($orders) {
                $file = fopen('php://output', 'w');
                
                // CSV Headers
                fputcsv($file, [
                    'Order Code',
                    'User Name',
                    'User Email',
                    'Currency',
                    'Amount',
                    'Total IDR',
                    'Exchange Rate',
                    'Status',
                    'Created At',
                    'Completed At'
                ]);

                // CSV Data
                foreach ($orders as $order) {
                    fputcsv($file, [
                        $order->order_code,
                        $order->user->name,
                        $order->user->email,
                        strtoupper($order->to_currency),
                        '$' . number_format($order->to_amount, 2),
                        'Rp ' . number_format($order->total_idr, 0, ',', '.'),
                        'Rp ' . number_format($order->rate ?? 0, 0, ',', '.'),
                        ucfirst($order->status),
                        $order->created_at->format('Y-m-d H:i:s'),
                        $order->completed_at ? $order->completed_at->format('Y-m-d H:i:s') : '-'
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error exporting data: ' . $e->getMessage()]);
        }
    }

    public function refreshStats()
    {
        try {
            $stats = [
                'pending_orders' => PurchaseOrder::where('status', 'pending')->count() + 
                                 ConversionOrder::where('status', 'pending')->count(),
                'processing_orders' => PurchaseOrder::where('status', 'processing')->count() + 
                                     ConversionOrder::where('status', 'processing')->count(),
                'success_orders' => PurchaseOrder::where('status', 'success')->count() + 
                                  ConversionOrder::where('status', 'success')->count(),
                'total_users' => User::where('role', 'user')->count(),
                'total_volume' => PurchaseOrder::where('status', 'success')->sum('total_idr') + 
                                ConversionOrder::where('status', 'success')->sum('total_idr'),
            ];

            return response()->json($stats);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // Dashboard method (existing)
    public function dashboard()
    {
        try {
            $totalUsers = User::where('role', 'user')->count();
            $totalConversionOrders = ConversionOrder::count();
            $totalPurchaseOrders = PurchaseOrder::count();
            $totalOrders = $totalConversionOrders + $totalPurchaseOrders;
            
            $pendingConversionOrders = ConversionOrder::where('status', 'pending')->count();
            $pendingPurchaseOrders = PurchaseOrder::where('status', 'pending')->count();
            $pendingOrders = $pendingConversionOrders + $pendingPurchaseOrders;
            
            $processingOrders = ConversionOrder::where('status', 'processing')->count() + 
                               PurchaseOrder::where('status', 'processing')->count();
            $successOrders = ConversionOrder::where('status', 'success')->count() + 
                            PurchaseOrder::where('status', 'success')->count();
            
            $conversionVolume = ConversionOrder::where('status', 'success')->sum('total_idr') ?? 0;
            $purchaseVolume = PurchaseOrder::where('status', 'success')->sum('total_idr') ?? 0;
            $totalVolume = $conversionVolume + $purchaseVolume;

            $recentConversionOrders = ConversionOrder::with(['user', 'paymentMethod'])
                ->latest()
                ->take(5)
                ->get()
                ->map(function($order) {
                    $order->order_type = 'conversion';
                    return $order;
                });
            
            $recentPurchaseOrders = PurchaseOrder::with(['user', 'paymentMethod'])
                ->latest()
                ->take(5)
                ->get()
                ->map(function($order) {
                    $order->order_type = 'purchase';
                    return $order;
                });
            
            $recentOrders = $recentConversionOrders->concat($recentPurchaseOrders)
                ->sortByDesc('created_at')
                ->take(10);

            return view('admin.dashboard', compact(
                'totalUsers', 
                'totalOrders',
                'totalConversionOrders',
                'totalPurchaseOrders',
                'pendingOrders', 
                'processingOrders',
                'successOrders',
                'totalVolume',
                'recentOrders'
            ));
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error loading dashboard: ' . $e->getMessage()]);
        }
    }

    private function getChartData()
    {
        $days = [];
        $purchaseData = [];
        $conversionData = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $days[] = $date->format('M d');
            
            $purchaseAmount = PurchaseOrder::whereDate('created_at', $date)
                ->where('status', 'success')
                ->sum('total_idr') ?? 0;
            $purchaseData[] = $purchaseAmount / 1000000; // Convert to millions
            
            $conversionAmount = ConversionOrder::whereDate('created_at', $date)
                ->where('status', 'success')
                ->sum('total_idr') ?? 0;
            $conversionData[] = $conversionAmount / 1000000; // Convert to millions
        }

        return [
            'labels' => $days,
            'purchase' => $purchaseData,
            'conversion' => $conversionData
        ];
    }

    // ===== CONVERSION ORDERS (JUAL PAYPAL/SKRILL) =====
    public function orders(Request $request)
    {
        try {
            $query = ConversionOrder::with(['user', 'paymentMethod']);

            // Apply filters
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('currency')) {
                $query->where('from_currency', $request->currency);
            }

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('order_code', 'like', "%{$search}%")
                      ->orWhereHas('user', function($userQuery) use ($search) {
                          $userQuery->where('name', 'like', "%{$search}%")
                                 ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $orders = $query->latest()->paginate(20);

        return view('admin.orders', compact('orders'));
    } catch (\Exception $e) {
        return back()->withErrors(['error' => 'Error loading orders: ' . $e->getMessage()]);
    }
}

public function showOrder($id)
{
    try {
        $order = ConversionOrder::with(['user', 'paymentMethod'])->findOrFail($id);
        
        // Load processedBy relationship if exists
        if ($order->processed_by) {
            $order->load('processedBy');
        }
        
        // Load admin account yang sesuai dengan payment method
        $adminAccount = AdminAccount::where('type', $this->getAccountTypeFromPaymentMethod($order->paymentMethod))
            ->where('is_active', true)
            ->first();

        return view('admin.order-detail', compact('order', 'adminAccount'));
    } catch (\Exception $e) {
        return back()->withErrors(['error' => 'Error loading order: ' . $e->getMessage()]);
    }
}

public function updateOrderStatus(Request $request, $id)
{
    $request->validate([
        'status' => 'required|in:pending,processing,success,failed,cancelled',
        'admin_notes' => 'nullable|string|max:1000',
    ]);

    try {
        DB::beginTransaction();

        $order = ConversionOrder::findOrFail($id);
        $oldStatus = $order->status;
        
        // Prepare update data
        $updateData = [
            'status' => $request->status,
            'admin_notes' => $request->admin_notes,
            'processed_by' => auth()->id(),
        ];

        // Set timestamps based on status
        if ($request->status === 'success') {
            $updateData['completed_at'] = now();
        } elseif ($request->status === 'cancelled') {
            $updateData['cancelled_at'] = now();
        }

        // Update order
        $order->update($updateData);

        DB::commit();
        return back()->with('success', 'Status order berhasil diupdate!');

    } catch (\Exception $e) {
        DB::rollback();
        return back()->withErrors(['error' => 'Gagal mengupdate status: ' . $e->getMessage()]);
    }
}

public function uploadProof(Request $request, $id)
{
    $request->validate([
        'payment_proof' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        'upload_notes' => 'nullable|string|max:500',
    ]);

    try {
        $order = ConversionOrder::findOrFail($id);

        // Store image
        $imageName = 'admin_' . time() . '.' . $request->payment_proof->extension();
        $request->payment_proof->move(public_path('uploads/proofs'), $imageName);

        // Update order
        $updateData = [
            'payment_proof' => 'uploads/proofs/' . $imageName,
            'status' => 'processing',
            'processed_by' => auth()->id(),
        ];

        if ($request->upload_notes) {
            $updateData['admin_notes'] = ($order->admin_notes ? $order->admin_notes . "\n\n" : '') . 
                "Admin Upload: " . $request->upload_notes;
        }

        $order->update($updateData);

        return back()->with('success', 'Bukti pembayaran berhasil diupload oleh admin!');
    } catch (\Exception $e) {
        return back()->withErrors(['error' => 'Gagal upload bukti: ' . $e->getMessage()]);
    }
}

public function destroyOrder($id)
{
    try {
        $order = ConversionOrder::findOrFail($id);
        
        // Delete payment proof file if exists
        if ($order->payment_proof && file_exists(public_path($order->payment_proof))) {
            unlink(public_path($order->payment_proof));
        }
        
        $order->delete();
        
        return redirect()->route('admin.orders')->with('success', 'Order berhasil dihapus!');
    } catch (\Exception $e) {
        return back()->withErrors(['error' => 'Gagal menghapus order: ' . $e->getMessage()]);
    }
}

// ===== PURCHASE ORDERS (BELI PAYPAL/SKRILL) =====
public function showPurchaseOrder($id)
{
    try {
        $order = PurchaseOrder::with(['user', 'paymentMethod'])->findOrFail($id);
        
        // Load processedBy relationship if exists
        if ($order->processed_by) {
            $order->load('processedBy');
        }
        
        // Load admin account yang sesuai dengan payment method  
        $adminAccount = AdminAccount::where('type', $this->getAccountTypeFromPaymentMethod($order->paymentMethod))
            ->where('is_active', true)
            ->first();

        return view('admin.purchase-order-detail', compact('order', 'adminAccount'));
    } catch (\Exception $e) {
        return back()->withErrors(['error' => 'Error loading purchase order: ' . $e->getMessage()]);
    }
}

public function updatePurchaseOrderStatus(Request $request, $id)
{
    $request->validate([
        'status' => 'required|in:pending,processing,success,failed,cancelled',
        'admin_notes' => 'nullable|string|max:1000',
    ]);

    try {
        DB::beginTransaction();

        $order = PurchaseOrder::findOrFail($id);
        
        // Prepare update data
        $updateData = [
            'status' => $request->status,
            'admin_notes' => $request->admin_notes,
            'processed_by' => auth()->id(),
        ];

        // Set timestamps based on status
        if ($request->status === 'success') {
            $updateData['completed_at'] = now();
        } elseif ($request->status === 'cancelled') {
            $updateData['cancelled_at'] = now();
        }

        // Update order
        $order->update($updateData);

        DB::commit();
        return back()->with('success', 'Status order pembelian berhasil diupdate!');

    } catch (\Exception $e) {
        DB::rollback();
        return back()->withErrors(['error' => 'Gagal mengupdate status: ' . $e->getMessage()]);
    }
}

public function uploadPurchaseProof(Request $request, $id)
{
    $request->validate([
        'payment_proof' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        'upload_notes' => 'nullable|string|max:500',
    ]);

    try {
        $order = PurchaseOrder::findOrFail($id);

        // Store image
        $imageName = 'admin_purchase_' . time() . '.' . $request->payment_proof->extension();
        $request->payment_proof->move(public_path('uploads/proofs'), $imageName);

        // Update order
        $updateData = [
            'payment_proof' => 'uploads/proofs/' . $imageName,
            'status' => 'processing',
            'processed_by' => auth()->id(),
        ];

        if ($request->upload_notes) {
            $updateData['admin_notes'] = ($order->admin_notes ? $order->admin_notes . "\n\n" : '') . 
                "Admin Upload: " . $request->upload_notes;
        }

        $order->update($updateData);

        return back()->with('success', 'Bukti pembayaran berhasil diupload oleh admin!');
    } catch (\Exception $e) {
        return back()->withErrors(['error' => 'Gagal upload bukti: ' . $e->getMessage()]);
    }
}

public function destroyPurchaseOrder($id)
{
    try {
        $order = PurchaseOrder::findOrFail($id);
        
        // Delete payment proof file if exists
        if ($order->payment_proof && file_exists(public_path($order->payment_proof))) {
            unlink(public_path($order->payment_proof));
        }
        
        $order->delete();
        
        return redirect()->route('admin.balance-orders')->with('success', 'Order pembelian berhasil dihapus!');
    } catch (\Exception $e) {
        return back()->withErrors(['error' => 'Gagal menghapus order: ' . $e->getMessage()]);
    }
}

    public function purchaseOrders(Request $request)
    {
        try {
            $query = PurchaseOrder::with(['user', 'paymentMethod']);

            // Apply filters
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('currency')) {
                $query->where('to_currency', $request->currency);
            }

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('order_code', 'like', "%{$search}%")
                      ->orWhereHas('user', function($userQuery) use ($search) {
                          $userQuery->where('name', 'like', "%{$search}%")
                                   ->orWhere('email', 'like', "%{$search}%");
                      });
                });
            }

            $orders = $query->latest()->paginate(20);

            return view('admin.purchase-orders', compact('orders'));
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error loading purchase orders: ' . $e->getMessage()]);
        }
    }

// ===== USER MANAGEMENT =====
public function users(Request $request)
{
    try {
        $query = User::withCount(['conversionOrders', 'purchaseOrders'])
            ->withSum('conversionOrders', 'total_idr')
            ->withSum('purchaseOrders', 'total_idr');

        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Apply role filter
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Apply status filter
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        // Apply date range filter
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // Apply sorting
        $sortBy = $request->get('sort', 'newest');
        switch ($sortBy) {
            case 'oldest':
                $query->oldest();
                break;
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'most_orders':
                $query->orderByDesc('conversion_orders_count')
                      ->orderByDesc('purchase_orders_count');
                break;
            case 'highest_volume':
                $query->orderByDesc('conversion_orders_sum_total_idr')
                      ->orderByDesc('purchase_orders_sum_total_idr');
                break;
            default:
                $query->latest();
                break;
        }

        // Pagination
        $perPage = $request->get('per_page', 20);
        $users = $query->paginate($perPage);

        // Calculate stats - THIS WAS MISSING THE $todayUsers variable
        $totalUsers = User::where('role', 'user')->count();
        $activeUsers = User::where('role', 'user')->where('is_active', true)->count();
        $todayUsers = User::where('role', 'user')->whereDate('created_at', today())->count();
        $adminUsers = User::where('role', 'admin')->count();
        $regularUsers = User::where('role', 'user')->count();

        return view('admin.users', compact(
            'users',
            'totalUsers',
            'activeUsers',
            'todayUsers',
            'adminUsers',
            'regularUsers'
        ));
    } catch (\Exception $e) {
        return back()->withErrors(['error' => 'Error loading users: ' . $e->getMessage()]);
    }
}

// ===== USER MANAGEMENT =====
public function showUser($id)
{
    try {
        $user = User::with(['conversionOrders.paymentMethod', 'purchaseOrders.paymentMethod'])->findOrFail($id);
        return view('admin.user-detail', compact('user'));
    } catch (\Exception $e) {
        return back()->withErrors(['error' => 'Error loading user: ' . $e->getMessage()]);
    }
}

public function toggleUserStatus($id)
{
    try {
        $user = User::findOrFail($id);
        
        if ($user->id === auth()->id()) {
            return back()->withErrors(['error' => 'Tidak dapat mengubah status akun sendiri']);
        }

        $user->update(['is_active' => !$user->is_active]);

        $status = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "User berhasil {$status}!");
    } catch (\Exception $e) {
        return back()->withErrors(['error' => 'Error updating user status: ' . $e->getMessage()]);
    }
}

public function makeAdmin($id)
{
    try {
        $user = User::findOrFail($id);
        
        if ($user->role === 'admin') {
            return back()->withErrors(['error' => 'User sudah menjadi admin']);
        }
        
        $user->update(['role' => 'admin']);

        return back()->with('success', 'User berhasil dijadikan admin!');
    } catch (\Exception $e) {
        return back()->withErrors(['error' => 'Error making user admin: ' . $e->getMessage()]);
    }
}

public function updateAccount(Request $request, $id)
{
    $request->validate([
        'type' => 'required|in:paypal,skrill,bank,ewallet',
        'name' => 'required|string|max:255',
        'account_number' => 'required|string|max:255',
        'account_name' => 'required|string|max:255',
        'notes' => 'nullable|string|max:500',
    ]);

    try {
        $account = AdminAccount::findOrFail($id);
        $account->update($request->all());
        return back()->with('success', 'Rekening admin berhasil diperbarui!');
    } catch (\Exception $e) {
        return back()->withErrors(['error' => 'Error updating account: ' . $e->getMessage()]);
    }
}

// ===== ADMIN ACCOUNTS MANAGEMENT =====
public function accounts()
{
    try {
        $accounts = AdminAccount::orderBy('type')->orderBy('name')->get();
        return view('admin.accounts', compact('accounts'));
    } catch (\Exception $e) {
        return back()->withErrors(['error' => 'Error loading accounts: ' . $e->getMessage()]);
    }
}

public function storeAccount(Request $request)
{
    $request->validate([
        'type' => 'required|in:paypal,skrill,bank,ewallet',
        'name' => 'required|string|max:255',
        'account_number' => 'required|string|max:255',
        'account_name' => 'required|string|max:255',
        'notes' => 'nullable|string|max:500',
    ]);

    try {
        AdminAccount::create($request->all());
        return back()->with('success', 'Rekening admin berhasil ditambahkan!');
    } catch (\Exception $e) {
        return back()->withErrors(['error' => 'Error creating account: ' . $e->getMessage()]);
    }
}

public function toggleAccount($id)
{
    try {
        $account = AdminAccount::findOrFail($id);
        $account->update(['is_active' => !$account->is_active]);

        $status = $account->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Rekening {$account->name} berhasil {$status}!");
    } catch (\Exception $e) {
        return back()->withErrors(['error' => 'Error toggling account: ' . $e->getMessage()]);
    }
}

public function destroyAccount($id)
{
    try {
        $account = AdminAccount::findOrFail($id);
        $account->delete();
        return back()->with('success', 'Rekening admin berhasil dihapus!');
    } catch (\Exception $e) {
        return back()->withErrors(['error' => 'Error deleting account: ' . $e->getMessage()]);
    }
}

// ===== EXCHANGE RATES MANAGEMENT =====
public function rates()
{
    try {
        $rates = ExchangeRate::with(['paymentMethod', 'updatedBy'])
            ->orderBy('from_currency')
            ->orderBy('created_at', 'desc')
            ->get();
            
        $paymentMethods = PaymentMethod::orderBy('type')->orderBy('name')->get();
        
        // Get admin accounts for reference - THIS WAS MISSING
        $accounts = AdminAccount::orderBy('type')->orderBy('name')->get();
        
        return view('admin.rates', compact('rates', 'paymentMethods', 'accounts'));
    } catch (\Exception $e) {
        return back()->withErrors(['error' => 'Error loading rates: ' . $e->getMessage()]);
    }
}

public function storeRate(Request $request)
{
    $request->validate([
        'from_currency' => 'required|in:paypal,skrill',
        'to_method_id' => 'required|exists:payment_methods,id',
        'rate' => 'required|numeric|min:1',
        'fee_percentage' => 'required|numeric|min:0|max:1',
        'admin_fee' => 'required|numeric|min:0',
    ]);

    try {
        // Check if rate already exists
        $existingRate = ExchangeRate::where('from_currency', $request->from_currency)
            ->where('to_method_id', $request->to_method_id)
            ->first();
        
        if ($existingRate) {
            return back()->withErrors(['error' => 'Rate untuk kombinasi ini sudah ada. Silakan edit yang sudah ada.']);
        }

        ExchangeRate::create([
            'from_currency' => $request->from_currency,
            'to_method_id' => $request->to_method_id,
            'rate' => $request->rate,
            'fee_percentage' => $request->fee_percentage,
            'admin_fee' => $request->admin_fee,
            'updated_by' => auth()->id(),
        ]);

        return back()->with('success', 'Exchange rate berhasil ditambahkan!');
    } catch (\Exception $e) {
        return back()->withErrors(['error' => 'Error creating rate: ' . $e->getMessage()]);
    }
}

public function updateRate(Request $request, $id)
{
    $request->validate([
        'rate' => 'required|numeric|min:1',
        'fee_percentage' => 'required|numeric|min:0|max:1',
        'admin_fee' => 'required|numeric|min:0',
    ]);

    try {
        $rate = ExchangeRate::findOrFail($id);
        $rate->update([
            'rate' => $request->rate,
            'fee_percentage' => $request->fee_percentage,
            'admin_fee' => $request->admin_fee,
            'updated_by' => auth()->id(),
        ]);

        return back()->with('success', 'Exchange rate berhasil diperbarui!');
    } catch (\Exception $e) {
        return back()->withErrors(['error' => 'Error updating rate: ' . $e->getMessage()]);
    }
}

public function toggleRate($id)
{
    try {
        $rate = ExchangeRate::findOrFail($id);
        $rate->update([
            'is_active' => !$rate->is_active,
            'updated_by' => auth()->id(),
        ]);

        $status = $rate->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Rate berhasil {$status}!");
    } catch (\Exception $e) {
        return back()->withErrors(['error' => 'Error toggling rate: ' . $e->getMessage()]);
    }
}

public function destroyRate($id)
{
    try {
        $rate = ExchangeRate::findOrFail($id);
        $rate->delete();
        return back()->with('success', 'Exchange rate berhasil dihapus!');
    } catch (\Exception $e) {
        return back()->withErrors(['error' => 'Error deleting rate: ' . $e->getMessage()]);
    }
}

// ===== PAYMENT METHODS MANAGEMENT =====
public function storePaymentMethod(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'code' => 'required|string|max:50|unique:payment_methods,code',
        'type' => 'required|in:ewallet,bank',
        'min_amount' => 'required|numeric|min:0',
        'max_amount' => 'required|numeric|min:0',
    ]);

    try {
        PaymentMethod::create($request->all());
        return back()->with('success', 'Metode pembayaran berhasil ditambahkan!');
    } catch (\Exception $e) {
        return back()->withErrors(['error' => 'Error creating payment method: ' . $e->getMessage()]);
    }
}

public function togglePaymentMethod($id)
{
    try {
        $method = PaymentMethod::findOrFail($id);
        $method->update(['is_active' => !$method->is_active]);

        $status = $method->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Metode {$method->name} berhasil {$status}!");
    } catch (\Exception $e) {
        return back()->withErrors(['error' => 'Error toggling payment method: ' . $e->getMessage()]);
    }
}

public function destroyPaymentMethod($id)
{
    try {
        $method = PaymentMethod::findOrFail($id);
        
        // Check if method is used in any rates
        $ratesCount = ExchangeRate::where('to_method_id', $id)->count();
        if ($ratesCount > 0) {
            return back()->withErrors(['error' => 'Tidak dapat menghapus metode yang masih digunakan dalam exchange rate.']);
        }
        
        $method->delete();
        return back()->with('success', 'Metode pembayaran berhasil dihapus!');
    } catch (\Exception $e) {
        return back()->withErrors(['error' => 'Error deleting payment method: ' . $e->getMessage()]);
    }
}

private function getAccountTypeFromPaymentMethod($paymentMethod)
{
    // Map payment method type to admin account type
    $typeMapping = [
        'ewallet' => 'ewallet',
        'bank' => 'bank'
    ];
    
    return $typeMapping[$paymentMethod->type] ?? 'bank';
}
}
