import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';
import { PageHeader, Badge } from '@/Components/UI';

export default function Show({ warehouse }) {
    return (
        <AuthenticatedLayout title={warehouse.name}><Head title={warehouse.name} />
            <PageHeader title={warehouse.name} subtitle={`${warehouse.code} — ${warehouse.city}`} />
            <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div className="bg-white rounded-xl border border-surface-200 p-6 text-sm space-y-3">
                    <div className="grid grid-cols-2 gap-4">
                        <div><span className="text-surface-500">Kode</span><p className="font-medium">{warehouse.code}</p></div>
                        <div><span className="text-surface-500">Kota</span><p className="font-medium">{warehouse.city}</p></div>
                        <div><span className="text-surface-500">Telepon</span><p className="font-medium">{warehouse.phone||'-'}</p></div>
                        <div><span className="text-surface-500">Status</span><p>{warehouse.is_active ? <Badge color="success">Aktif</Badge> : <Badge color="danger">Nonaktif</Badge>}</p></div>
                    </div>
                    {warehouse.address && <div><span className="text-surface-500">Alamat</span><p>{warehouse.address}</p></div>}
                </div>
                <div className="bg-white rounded-xl border border-surface-200 p-5">
                    <h3 className="text-sm font-semibold text-surface-900 mb-3">Stok Produk</h3>
                    {warehouse.inventory_stocks?.length > 0 ? (
                        <div className="space-y-2 max-h-80 overflow-y-auto">
                            {warehouse.inventory_stocks.map(s => (
                                <div key={s.id} className="flex justify-between items-center text-sm py-2 border-b border-surface-100">
                                    <div><p className="font-medium">{s.product?.name}</p><p className="text-xs text-surface-500">{s.product?.sku}</p></div>
                                    <span className={`font-bold ${s.quantity <= (s.product?.minimum_stock||0) ? 'text-red-600' : ''}`}>{s.quantity}</span>
                                </div>
                            ))}
                        </div>
                    ) : <p className="text-surface-400 text-sm">Belum ada stok</p>}
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
