<?php

namespace App\Http\Controllers;

use App\Models\InventoryStock;
use App\Models\StockTransaction;
use App\Models\StockTransfer;
use App\Services\AuditLogService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ExportController extends Controller
{
    public function index()
    {
        return Inertia::render('Reports/Index');
    }

    public function inventoryReport(Request $request)
    {
        $data = DB::table('inventory_stocks')
            ->join('products','inventory_stocks.product_id','=','products.id')
            ->join('warehouses','inventory_stocks.warehouse_id','=','warehouses.id')
            ->join('categories','products.category_id','=','categories.id')
            ->select('products.sku','products.name as product','categories.name as category',
                'warehouses.name as warehouse','inventory_stocks.quantity',
                'products.minimum_stock','products.selling_price')
            ->when($request->warehouse_id, fn($q,$v)=>$q->where('warehouses.id',$v))
            ->orderBy('products.name')->get();

        $format = $request->input('format','pdf');

        if ($format === 'csv') {
            return $this->exportCsv('inventory_report', ['SKU','Product','Category','Warehouse','Qty','Min Stock','Price'], $data->map(fn($r)=>[$r->sku,$r->product,$r->category,$r->warehouse,$r->quantity,$r->minimum_stock,$r->selling_price])->toArray());
        }

        $pdf = Pdf::loadView('exports.inventory', ['data'=>$data, 'title'=>'Laporan Stok Inventory', 'date'=>now()->format('d/m/Y')]);
        AuditLogService::log('export','reports','Exported inventory report');
        return $pdf->download('inventory_report_'.now()->format('Ymd').'.pdf');
    }

    public function lowStockReport(Request $request)
    {
        $data = DB::table('inventory_stocks')
            ->join('products','inventory_stocks.product_id','=','products.id')
            ->join('warehouses','inventory_stocks.warehouse_id','=','warehouses.id')
            ->whereColumn('inventory_stocks.quantity','<=','products.minimum_stock')
            ->select('products.sku','products.name as product','warehouses.name as warehouse',
                'inventory_stocks.quantity','products.minimum_stock')
            ->orderBy('inventory_stocks.quantity')->get();

        $pdf = Pdf::loadView('exports.low_stock', ['data'=>$data, 'title'=>'Laporan Stok Rendah', 'date'=>now()->format('d/m/Y')]);
        AuditLogService::log('export','reports','Exported low stock report');
        return $pdf->download('low_stock_report_'.now()->format('Ymd').'.pdf');
    }

    public function transactionReport(Request $request)
    {
        $query = StockTransaction::with(['product:id,name,sku','warehouse:id,name','creator:id,name']);
        if ($request->date_from) $query->where('transaction_date','>=',$request->date_from);
        if ($request->date_to) $query->where('transaction_date','<=',$request->date_to);
        if ($request->type) $query->where('type',$request->type);
        $data = $query->orderBy('transaction_date','desc')->get();

        $format = $request->input('format','pdf');
        if ($format === 'csv') {
            return $this->exportCsv('transaction_report', ['Code','Type','Product','Warehouse','Qty','Date','Created By'], $data->map(fn($r)=>[$r->transaction_code,$r->type,$r->product->name,$r->warehouse->name,$r->quantity,$r->transaction_date->format('d/m/Y'),$r->creator->name])->toArray());
        }

        $pdf = Pdf::loadView('exports.transactions', ['data'=>$data, 'title'=>'Laporan Transaksi Stok', 'date'=>now()->format('d/m/Y')]);
        AuditLogService::log('export','reports','Exported transaction report');
        return $pdf->download('transaction_report_'.now()->format('Ymd').'.pdf');
    }

    public function transferReport(Request $request)
    {
        $query = StockTransfer::with(['product:id,name,sku','sourceWarehouse:id,name','destinationWarehouse:id,name','creator:id,name']);
        if ($request->date_from) $query->where('transfer_date','>=',$request->date_from);
        if ($request->date_to) $query->where('transfer_date','<=',$request->date_to);
        $data = $query->orderBy('transfer_date','desc')->get();

        $pdf = Pdf::loadView('exports.transfers', ['data'=>$data, 'title'=>'Laporan Transfer Gudang', 'date'=>now()->format('d/m/Y')]);
        AuditLogService::log('export','reports','Exported transfer report');
        return $pdf->download('transfer_report_'.now()->format('Ymd').'.pdf');
    }

    private function exportCsv(string $filename, array $headers, array $rows)
    {
        $csv = implode(',', $headers)."\n";
        foreach ($rows as $row) { $csv .= implode(',', $row)."\n"; }
        return response($csv, 200, ['Content-Type'=>'text/csv','Content-Disposition'=>"attachment; filename={$filename}_".now()->format('Ymd').".csv"]);
    }
}
