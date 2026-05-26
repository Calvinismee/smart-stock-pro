import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link } from '@inertiajs/react';
import { Package, Tags, Building2, Truck, Layers, AlertTriangle, ArrowDownToLine, ArrowUpFromLine, TrendingUp } from 'lucide-react';
import { AreaChart, Area, XAxis, YAxis, Tooltip, ResponsiveContainer, CartesianGrid } from 'recharts';

function StatCard({ label, value, icon: Icon, color = 'primary', href }) {
    const colors = {
        primary: 'bg-primary-50 text-primary-600', success: 'bg-green-50 text-green-600',
        warning: 'bg-amber-50 text-amber-600', danger: 'bg-red-50 text-red-600',
        info: 'bg-blue-50 text-blue-600', purple: 'bg-purple-50 text-purple-600',
    };
    const Card = href ? Link : 'div';
    return (
        <Card href={href} className="bg-white rounded-xl border border-surface-200 p-5 hover:shadow-md transition-shadow">
            <div className="flex items-center justify-between">
                <div>
                    <p className="text-sm text-surface-500">{label}</p>
                    <p className="text-2xl font-bold text-surface-900 mt-1">{typeof value === 'number' ? value.toLocaleString('id-ID') : value}</p>
                </div>
                <div className={`w-12 h-12 rounded-xl flex items-center justify-center ${colors[color]}`}>
                    <Icon size={24} />
                </div>
            </div>
        </Card>
    );
}

function formatRupiah(v) {
    return 'Rp ' + Number(v || 0).toLocaleString('id-ID');
}

export default function Dashboard({ stats, stockTrends, recentTransactions, recentTransfers, lowStockProducts, warehouseMapData }) {
    return (
        <AuthenticatedLayout title="Dashboard">
            <Head title="Dashboard" />
            <div className="space-y-6">
                {/* Stat cards */}
                <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <StatCard label="Total Produk" value={stats.total_products} icon={Package} color="primary" href="/products" />
                    <StatCard label="Total Kategori" value={stats.total_categories} icon={Tags} color="info" href="/categories" />
                    <StatCard label="Total Gudang" value={stats.total_warehouses} icon={Building2} color="success" href="/warehouses" />
                    <StatCard label="Total Supplier" value={stats.total_suppliers} icon={Truck} color="purple" href="/suppliers" />
                </div>
                <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <StatCard label="Total Stok" value={stats.total_stock} icon={Layers} color="primary" />
                    <StatCard label="Stok Rendah" value={stats.low_stock_count} icon={AlertTriangle} color={stats.low_stock_count > 0 ? 'danger' : 'success'} />
                    <StatCard label="Nilai Inventory" value={formatRupiah(stats.inventory_value)} icon={TrendingUp} color="info" />
                </div>

                {/* Chart + Low stock */}
                <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div className="lg:col-span-2 bg-white rounded-xl border border-surface-200 p-5">
                        <h3 className="text-base font-semibold text-surface-900 mb-4">Tren Stok Masuk & Keluar (30 Hari)</h3>
                        {stockTrends?.length > 0 ? (
                            <ResponsiveContainer width="100%" height={280}>
                                <AreaChart data={stockTrends}>
                                    <CartesianGrid strokeDasharray="3 3" stroke="#e5e7eb" />
                                    <XAxis dataKey="date" tick={{ fontSize: 11 }} />
                                    <YAxis tick={{ fontSize: 11 }} />
                                    <Tooltip />
                                    <Area type="monotone" dataKey="stock_in" name="Masuk" stroke="#22c55e" fill="#22c55e" fillOpacity={0.15} strokeWidth={2} />
                                    <Area type="monotone" dataKey="stock_out" name="Keluar" stroke="#ef4444" fill="#ef4444" fillOpacity={0.15} strokeWidth={2} />
                                </AreaChart>
                            </ResponsiveContainer>
                        ) : <p className="text-surface-400 text-sm py-10 text-center">Belum ada data transaksi</p>}
                    </div>
                    <div className="bg-white rounded-xl border border-surface-200 p-5">
                        <h3 className="text-base font-semibold text-surface-900 mb-4 flex items-center gap-2"><AlertTriangle size={18} className="text-amber-500" /> Stok Rendah</h3>
                        {lowStockProducts?.length > 0 ? (
                            <div className="space-y-3 max-h-72 overflow-y-auto">
                                {lowStockProducts.map((p, i) => (
                                    <div key={i} className="flex items-center justify-between py-2 border-b border-surface-100 last:border-0">
                                        <div>
                                            <p className="text-sm font-medium text-surface-900">{p.product_name}</p>
                                            <p className="text-xs text-surface-500">{p.warehouse_name}</p>
                                        </div>
                                        <span className={`text-sm font-bold ${p.quantity <= 0 ? 'text-red-600' : 'text-amber-600'}`}>
                                            {p.quantity}/{p.minimum_stock}
                                        </span>
                                    </div>
                                ))}
                            </div>
                        ) : <p className="text-surface-400 text-sm py-10 text-center">Semua stok aman ✓</p>}
                    </div>
                </div>

                {/* Recent tables */}
                <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div className="bg-white rounded-xl border border-surface-200 p-5">
                        <h3 className="text-base font-semibold text-surface-900 mb-4">Transaksi Terakhir</h3>
                        {recentTransactions?.length > 0 ? (
                            <div className="overflow-x-auto">
                                <table className="w-full text-sm">
                                    <thead><tr className="border-b border-surface-200 text-left text-surface-500">
                                        <th className="pb-2 font-medium">Kode</th><th className="pb-2 font-medium">Tipe</th><th className="pb-2 font-medium">Produk</th><th className="pb-2 font-medium text-right">Qty</th>
                                    </tr></thead>
                                    <tbody>
                                        {recentTransactions.map(t => (
                                            <tr key={t.id} className="border-b border-surface-100">
                                                <td className="py-2 text-xs text-surface-600">{t.transaction_code}</td>
                                                <td className="py-2"><span className={`px-2 py-0.5 text-xs rounded-full font-medium ${t.type==='in' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'}`}>{t.type==='in'?'Masuk':'Keluar'}</span></td>
                                                <td className="py-2 text-surface-900">{t.product?.name}</td>
                                                <td className="py-2 text-right font-medium">{t.quantity}</td>
                                            </tr>
                                        ))}
                                    </tbody>
                                </table>
                            </div>
                        ) : <p className="text-surface-400 text-sm py-6 text-center">Belum ada transaksi</p>}
                    </div>
                    <div className="bg-white rounded-xl border border-surface-200 p-5">
                        <h3 className="text-base font-semibold text-surface-900 mb-4">Transfer Terakhir</h3>
                        {recentTransfers?.length > 0 ? (
                            <div className="overflow-x-auto">
                                <table className="w-full text-sm">
                                    <thead><tr className="border-b border-surface-200 text-left text-surface-500">
                                        <th className="pb-2 font-medium">Kode</th><th className="pb-2 font-medium">Produk</th><th className="pb-2 font-medium">Rute</th><th className="pb-2 font-medium text-right">Qty</th>
                                    </tr></thead>
                                    <tbody>
                                        {recentTransfers.map(t => (
                                            <tr key={t.id} className="border-b border-surface-100">
                                                <td className="py-2 text-xs text-surface-600">{t.transfer_code}</td>
                                                <td className="py-2 text-surface-900">{t.product?.name}</td>
                                                <td className="py-2 text-xs text-surface-600">{t.source_warehouse?.name} → {t.destination_warehouse?.name}</td>
                                                <td className="py-2 text-right font-medium">{t.quantity}</td>
                                            </tr>
                                        ))}
                                    </tbody>
                                </table>
                            </div>
                        ) : <p className="text-surface-400 text-sm py-6 text-center">Belum ada transfer</p>}
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
