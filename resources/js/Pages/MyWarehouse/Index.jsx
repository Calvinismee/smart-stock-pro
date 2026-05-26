import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link } from '@inertiajs/react';
import { PageHeader, LinkButton, Badge } from '@/Components/UI';
import { Building2, Package, ArrowDownToLine, ArrowUpFromLine, ArrowRightLeft, AlertTriangle } from 'lucide-react';
import dayjs from 'dayjs';

export default function Index({ warehouse, stats, lowStockItems, recentStockIns, recentStockOuts, recentTransfers }) {
    return (
        <AuthenticatedLayout title="My Warehouse">
            <Head title={`My Warehouse - ${warehouse.name}`} />
            
            <div className="flex flex-col md:flex-row md:items-center justify-between mb-6 gap-4">
                <div>
                    <h1 className="text-2xl font-bold text-surface-900">{warehouse.name}</h1>
                    <p className="text-surface-500 text-sm mt-1">{warehouse.city} - {warehouse.address}</p>
                </div>
                <div className="flex gap-2">
                    <LinkButton href="/stock-transactions?modal=in"><ArrowDownToLine size={16} className="mr-1 inline"/> Masuk</LinkButton>
                    <LinkButton href="/stock-transactions?modal=out" color="danger"><ArrowUpFromLine size={16} className="mr-1 inline"/> Keluar</LinkButton>
                    <LinkButton href="/stock-transfers?modal=true" color="secondary"><ArrowRightLeft size={16} className="mr-1 inline"/> Transfer</LinkButton>
                </div>
            </div>

            <div className="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div className="bg-white rounded-xl p-5 border border-surface-200 flex items-center gap-4">
                    <div className="w-12 h-12 rounded-full bg-primary-100 text-primary-600 flex items-center justify-center flex-shrink-0">
                        <Package size={24} />
                    </div>
                    <div>
                        <p className="text-sm font-medium text-surface-500">Total Item Produk (Ada Stok)</p>
                        <h3 className="text-2xl font-bold text-surface-900">{stats.totalItems} <span className="text-sm font-normal text-surface-500">SKU</span></h3>
                    </div>
                </div>
                
                <div className="bg-white rounded-xl p-5 border border-surface-200 flex items-center gap-4">
                    <div className="w-12 h-12 rounded-full bg-red-100 text-red-600 flex items-center justify-center flex-shrink-0">
                        <AlertTriangle size={24} />
                    </div>
                    <div>
                        <p className="text-sm font-medium text-surface-500">Produk Stok Menipis</p>
                        <h3 className="text-2xl font-bold text-surface-900">{stats.lowStockCount} <span className="text-sm font-normal text-surface-500">item</span></h3>
                    </div>
                </div>
            </div>

            <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div className="bg-white rounded-xl border border-surface-200 overflow-hidden">
                    <div className="p-4 border-b border-surface-100 bg-surface-50 flex justify-between items-center">
                        <h3 className="font-semibold text-surface-900 flex items-center gap-2"><ArrowDownToLine size={16} className="text-primary-600"/> Barang Masuk Terakhir</h3>
                        <Link href="/stock-transactions?type=in" className="text-xs text-primary-600 hover:underline">Lihat Semua</Link>
                    </div>
                    <div className="divide-y divide-surface-100">
                        {recentStockIns.length > 0 ? recentStockIns.map(tx => (
                            <div key={tx.id} className="p-4 text-sm flex justify-between items-center hover:bg-surface-50">
                                <div>
                                    <p className="font-medium text-surface-900">{tx.product?.name}</p>
                                    <p className="text-xs text-surface-500">{tx.transaction_code} &bull; {dayjs(tx.transaction_date).format('DD MMM YYYY')}</p>
                                </div>
                                <span className="font-bold text-green-600">+{tx.quantity}</span>
                            </div>
                        )) : <div className="p-8 text-center text-surface-500 text-sm">Belum ada transaksi.</div>}
                    </div>
                </div>

                <div className="bg-white rounded-xl border border-surface-200 overflow-hidden">
                    <div className="p-4 border-b border-surface-100 bg-surface-50 flex justify-between items-center">
                        <h3 className="font-semibold text-surface-900 flex items-center gap-2"><ArrowUpFromLine size={16} className="text-red-600"/> Barang Keluar Terakhir</h3>
                        <Link href="/stock-transactions?type=out" className="text-xs text-primary-600 hover:underline">Lihat Semua</Link>
                    </div>
                    <div className="divide-y divide-surface-100">
                        {recentStockOuts.length > 0 ? recentStockOuts.map(tx => (
                            <div key={tx.id} className="p-4 text-sm flex justify-between items-center hover:bg-surface-50">
                                <div>
                                    <p className="font-medium text-surface-900">{tx.product?.name}</p>
                                    <p className="text-xs text-surface-500">{tx.transaction_code} &bull; {dayjs(tx.transaction_date).format('DD MMM YYYY')}</p>
                                </div>
                                <span className="font-bold text-red-600">-{tx.quantity}</span>
                            </div>
                        )) : <div className="p-8 text-center text-surface-500 text-sm">Belum ada transaksi.</div>}
                    </div>
                </div>

                <div className="bg-white rounded-xl border border-surface-200 overflow-hidden lg:col-span-2">
                    <div className="p-4 border-b border-surface-100 bg-surface-50 flex justify-between items-center">
                        <h3 className="font-semibold text-surface-900 flex items-center gap-2"><ArrowRightLeft size={16} className="text-orange-500"/> Transfer Terakhir</h3>
                        <Link href="/stock-transfers" className="text-xs text-primary-600 hover:underline">Lihat Semua</Link>
                    </div>
                    <div className="divide-y divide-surface-100">
                        {recentTransfers.length > 0 ? recentTransfers.map(tx => (
                            <div key={tx.id} className="p-4 text-sm flex justify-between items-center hover:bg-surface-50">
                                <div>
                                    <p className="font-medium text-surface-900">{tx.product?.name}</p>
                                    <p className="text-xs text-surface-500">{tx.transfer_code} &bull; {dayjs(tx.transfer_date).format('DD MMM YYYY')} &bull; Ke {tx.destination_warehouse?.name}</p>
                                </div>
                                <div className="text-right">
                                    <span className="font-bold text-orange-600">-{tx.quantity}</span>
                                    <div><Badge color="success">Selesai</Badge></div>
                                </div>
                            </div>
                        )) : <div className="p-8 text-center text-surface-500 text-sm">Belum ada transfer.</div>}
                    </div>
                </div>
            </div>
            
            {lowStockItems.length > 0 && (
                <div className="mt-6 bg-red-50 border border-red-200 rounded-xl p-5">
                    <h3 className="font-bold text-red-800 mb-3 flex items-center gap-2"><AlertTriangle size={18}/> Perhatian: Stok Menipis/Kritis!</h3>
                    <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                        {lowStockItems.map(item => (
                            <div key={item.id} className="bg-white p-3 rounded-lg shadow-sm border border-red-100">
                                <p className="font-medium text-surface-900 text-sm truncate" title={item.product?.name}>{item.product?.name}</p>
                                <div className="flex justify-between items-center mt-2">
                                    <span className="text-xs text-surface-500">Sisa:</span>
                                    <span className="font-bold text-red-600">{item.quantity}</span>
                                </div>
                            </div>
                        ))}
                    </div>
                    <div className="mt-4">
                        <Link href="/inventory-stocks?status=menipis" className="text-sm text-red-700 hover:underline font-medium">Lihat semua stok menipis &rarr;</Link>
                    </div>
                </div>
            )}
        </AuthenticatedLayout>
    );
}
