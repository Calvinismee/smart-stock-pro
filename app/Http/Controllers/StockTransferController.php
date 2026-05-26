<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockTransfer;
use App\Models\Warehouse;
use App\Services\StockTransferService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class StockTransferController extends Controller
{
    public function __construct(private StockTransferService $service) {}

    public function index(Request $request)
    {
        $query = StockTransfer::with(['product:id,name,sku','sourceWarehouse:id,name','destinationWarehouse:id,name','creator:id,name']);
        if ($s = $request->input('search')) {
            $query->where(fn($q)=>$q->where('transfer_code','ilike',"%{$s}%")->orWhereHas('product',fn($q2)=>$q2->where('name','ilike',"%{$s}%")));
        }
        $query->orderBy($request->input('sort','created_at'), $request->input('direction','desc'));

        return Inertia::render('StockTransfers/Index', [
            'transfers' => $query->paginate(15)->withQueryString(),
            'filters' => $request->only(['search','sort','direction']),
        ]);
    }

    public function create()
    {
        return Inertia::render('StockTransfers/Create', [
            'products' => Product::where('is_active',true)->select('id','name','sku')->get(),
            'warehouses' => Warehouse::where('is_active',true)->select('id','name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'product_id'=>'required|exists:products,id',
            'source_warehouse_id'=>'required|exists:warehouses,id',
            'destination_warehouse_id'=>'required|exists:warehouses,id|different:source_warehouse_id',
            'quantity'=>'required|integer|min:1','transfer_date'=>'required|date','notes'=>'nullable|string',
        ]);
        try {
            $this->service->transfer($data, auth()->id());
            return redirect()->route('stock-transfers.index')->with('success','Transfer gudang berhasil.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function show(StockTransfer $stockTransfer)
    {
        $stockTransfer->load(['product','sourceWarehouse','destinationWarehouse','creator']);
        return Inertia::render('StockTransfers/Show', ['transfer' => $stockTransfer]);
    }
}
