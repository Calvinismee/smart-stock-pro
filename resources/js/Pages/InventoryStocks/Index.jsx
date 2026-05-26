import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, usePage } from '@inertiajs/react';
import { DataTable, PageHeader, Badge } from '@/Components/UI';
import { Box, Package } from 'lucide-react';

export default function Index({ stocks, categories, warehouses, filters }) {
    const { auth } = usePage().props;
    const canViewProducts = ['admin', 'manager', 'auditor'].includes(auth.user.role);
    const columns = [
        { key: 'product.sku', label: 'SKU', sortable: false, render: r => <span className="font-mono text-xs">{r.product?.sku}</span> },
        { key: 'product.name', label: 'Produk', sortable: false, render: r => (
            <div>
                <p className="font-medium text-surface-900">{r.product?.name}</p>
                <p className="text-xs text-surface-500">{r.product?.category?.name}</p>
            </div>
        )},
        { key: 'warehouse.name', label: 'Gudang', sortable: false, render: r => r.warehouse?.name },
        { key: 'quantity', label: 'Kuantitas', sortable: true, render: r => {
            const minStock = r.minimum_stock > 0 ? r.minimum_stock : (r.product?.minimum_stock || 0);
            return <span className={`font-bold ${r.quantity <= minStock ? 'text-red-600' : 'text-surface-900'}`}>{r.quantity}</span>
        }},
        { key: 'minimum_stock', label: 'Stok Minimum', sortable: false, render: r => {
            const minStock = r.minimum_stock > 0 ? r.minimum_stock : (r.product?.minimum_stock || 0);
            return <span className="text-surface-500">{minStock}</span>
        }},
        { key: 'status', label: 'Status', sortable: false, render: r => {
            const minStock = r.minimum_stock > 0 ? r.minimum_stock : (r.product?.minimum_stock || 0);
            if (r.quantity === 0) return <Badge color="danger">Kritis</Badge>;
            if (r.quantity <= minStock) return <Badge color="warning">Menipis</Badge>;
            return <Badge color="success">Aman</Badge>;
        }},
    ];

    const extraFilters = (
        <>
            <select
                name="status"
                className="w-full md:w-48 text-sm rounded-lg border-surface-300 focus:border-primary-500 focus:ring-primary-500"
                defaultValue={filters.status || ''}
                onChange={(e) => {
                    const url = new URL(window.location.href);
                    if (e.target.value) {
                        url.searchParams.set('status', e.target.value);
                    } else {
                        url.searchParams.delete('status');
                    }
                    url.searchParams.delete('page');
                    window.location.href = url.toString();
                }}
            >
                <option value="">Semua Status</option>
                <option value="aman">Aman</option>
                <option value="menipis">Menipis</option>
                <option value="kritis">Kritis</option>
            </select>
            <select
                name="warehouse_id"
                className="w-full md:w-48 text-sm rounded-lg border-surface-300 focus:border-primary-500 focus:ring-primary-500"
                defaultValue={filters.warehouse_id || ''}
                onChange={(e) => {
                    const url = new URL(window.location.href);
                    if (e.target.value) {
                        url.searchParams.set('warehouse_id', e.target.value);
                    } else {
                        url.searchParams.delete('warehouse_id');
                    }
                    url.searchParams.delete('page');
                    window.location.href = url.toString();
                }}
            >
                <option value="">Semua Gudang</option>
                {warehouses.map(w => (
                    <option key={w.id} value={w.id}>{w.name}</option>
                ))}
            </select>
        </>
    );

    return (
        <AuthenticatedLayout title={canViewProducts ? "Katalog & Stok" : "Monitoring Stok"}>
            <Head title="Monitoring Stok" />
            
            {canViewProducts && (
                <div className="border-b border-surface-200 mb-6">
                    <nav className="-mb-px flex gap-6">
                        <Link href="/products" className={`py-4 px-1 inline-flex items-center gap-2 border-b-2 font-medium text-sm border-transparent text-surface-500 hover:text-surface-700 hover:border-surface-300`}>
                            <Package size={18} /> Master Produk
                        </Link>
                        <Link href="/inventory-stocks" className={`py-4 px-1 inline-flex items-center gap-2 border-b-2 font-medium text-sm border-primary-500 text-primary-600`}>
                            <Box size={18} /> Monitoring Stok
                        </Link>
                    </nav>
                </div>
            )}

            <PageHeader 
                title="Monitoring Stok" 
                subtitle="Pantau kuantitas stok produk di masing-masing gudang"
                icon={<Box className="w-8 h-8 text-primary-600" />}
            />
            <DataTable 
                columns={columns} 
                data={stocks.data} 
                pagination={stocks} 
                filters={filters} 
                searchPlaceholder="Cari SKU atau nama produk..."
                extraFilters={extraFilters}
            />
        </AuthenticatedLayout>
    );
}
