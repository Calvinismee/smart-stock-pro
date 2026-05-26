<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockTransaction;
use App\Models\Supplier;
use App\Models\Warehouse;
use App\Services\StockTransactionService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class StockTransactionController extends Controller
{
    public function __construct(private StockTransactionService $service) {}

    public function index(Request $request)
    {
        $query = StockTransaction::with(['product:id,name,sku','warehouse:id,name','supplier:id,name','creator:id,name']);
        if ($s = $request->input('search')) {
            $query->where(fn($q)=>$q->where('transaction_code','ilike',"%{$s}%")->orWhereHas('product',fn($q2)=>$q2->where('name','ilike',"%{$s}%")));
        }
        if ($type = $request->input('type')) { $query->where('type', $type); }
        if ($wid = $request->input('warehouse_id')) { $query->where('warehouse_id', $wid); }
        $query->orderBy($request->input('sort','created_at'), $request->input('direction','desc'));

        return Inertia::render('StockTransactions/Index', [
            'transactions' => $query->paginate(15)->withQueryString(),
            'warehouses' => Warehouse::select('id','name')->get(),
            'filters' => $request->only(['search','type','warehouse_id','sort','direction']),
        ]);
    }

    public function createIn()
    {
        return Inertia::render('StockTransactions/CreateIn', [
            'products' => Product::where('is_active',true)->select('id','name','sku')->get(),
            'warehouses' => Warehouse::where('is_active',true)->select('id','name')->get(),
            'suppliers' => Supplier::select('id','name')->get(),
        ]);
    }

    public function storeIn(Request $request)
    {
        $data = $request->validate([
            'product_id'=>'required|exists:products,id','warehouse_id'=>'required|exists:warehouses,id',
            'supplier_id'=>'nullable|exists:suppliers,id','quantity'=>'required|integer|min:1',
            'transaction_date'=>'required|date','notes'=>'nullable|string',
        ]);
        $this->service->stockIn($data, auth()->id());
        return redirect()->route('stock-transactions.index')->with('success','Barang masuk berhasil dicatat.');
    }

    public function createOut()
    {
        return Inertia::render('StockTransactions/CreateOut', [
            'products' => Product::where('is_active',true)->select('id','name','sku')->get(),
            'warehouses' => Warehouse::where('is_active',true)->select('id','name')->get(),
        ]);
    }

    public function storeOut(Request $request)
    {
        $data = $request->validate([
            'product_id'=>'required|exists:products,id','warehouse_id'=>'required|exists:warehouses,id',
            'quantity'=>'required|integer|min:1','transaction_date'=>'required|date','notes'=>'nullable|string',
        ]);
        try {
            $this->service->stockOut($data, auth()->id());
            return redirect()->route('stock-transactions.index')->with('success','Barang keluar berhasil dicatat.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }
}
