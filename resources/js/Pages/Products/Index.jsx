import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, usePage } from '@inertiajs/react';
import { DataTable, PageHeader, Badge, DeleteButton, LinkButton } from '@/Components/UI';
import { Plus, Eye, Pencil, Building2, Globe, Package, Box } from 'lucide-react';
import { useState } from 'react';

export default function Index({ products, categories, warehouses, filters }) {
    const { auth } = usePage().props;
    const isStaff = auth.user.role === 'staff';
    const isAdmin = auth.user.role === 'admin';
    const isAuditor = auth.user.role === 'auditor';
    const canMutate = ['admin', 'manager'].includes(auth.user.role);
    const [showGlobal, setShowGlobal] = useState(!isStaff);
    
    const selectedWarehouse = warehouses?.find(w => w.id == filters.warehouse_id);
    const stockLabel = selectedWarehouse ? `Stok ${selectedWarehouse.name}` : 'Stok Global';

    const columns = [
        { key: 'sku', label: 'SKU', sortable: true, render: r => <span className="font-mono text-xs">{r.sku}</span> },
        { key: 'name', label: 'Nama Produk', sortable: true, render: r => (
            <div className="flex items-center gap-3">
                {r.image ? <img src={`/storage/${r.image}`} className="w-10 h-10 rounded-lg object-cover" /> : <div className="w-10 h-10 rounded-lg bg-surface-100 flex items-center justify-center text-surface-400 text-xs">No img</div>}
                <div><p className="font-medium text-surface-900">{r.name}</p><p className="text-xs text-surface-500">{r.category?.name}</p></div>
            </div>
        )},
        { key: 'selling_price', label: 'Harga Jual', sortable: true, render: r => 'Rp ' + Number(r.selling_price).toLocaleString('id-ID') },
        { key: 'stock', label: stockLabel, sortable: false, render: r => {
            const globalQty = r.inventory_stocks_sum_quantity || 0;
            const myQty = r.warehouse_stock || 0;
            const qty = showGlobal ? globalQty : myQty;
            
            return (
                <div className="flex items-center gap-1.5">
                    <span className={qty <= r.minimum_stock ? 'text-red-600 font-bold' : ''}>{qty}</span>
                    {isStaff && (
                        <button 
                            onClick={(e) => { e.stopPropagation(); setShowGlobal(!showGlobal); }}
                            className="text-surface-400 hover:text-primary-600 ml-1"
                            title={showGlobal ? "Lihat stok gudang Anda" : "Lihat stok semua gudang"}
                        >
                            {showGlobal ? <Building2 size={14}/> : <Globe size={14}/>}
                        </button>
                    )}
                </div>
            );
        }},
        { key: 'is_active', label: 'Status', render: r => r.is_active ? <Badge color="success">Aktif</Badge> : <Badge color="danger">Nonaktif</Badge> },
        { key: 'actions', label: 'Aksi', render: r => (
            <div className="flex items-center gap-3">
                <Link href={`/products/${r.id}`} className="text-surface-500 hover:text-primary-600"><Eye size={16}/></Link>
                {canMutate && <Link href={`/products/${r.id}/edit`} className="text-surface-500 hover:text-primary-600"><Pencil size={16}/></Link>}
                {isAdmin && <DeleteButton href={`/products/${r.id}`} name={r.name} />}
            </div>
        )},
    ];

    const extraFilters = (
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
            {warehouses?.map(w => (
                <option key={w.id} value={w.id}>{w.name}</option>
            ))}
        </select>
    );

    return (
        <AuthenticatedLayout title="Katalog Produk">
            <Head title="Produk" />

            <div className="border-b border-surface-200 mb-6">
                <nav className="-mb-px flex gap-6">
                    <Link href="/products" className={`py-4 px-1 inline-flex items-center gap-2 border-b-2 font-medium text-sm border-primary-500 text-primary-600`}>
                        <Package size={18} /> Master Produk
                    </Link>
                    <Link href="/inventory-stocks" className={`py-4 px-1 inline-flex items-center gap-2 border-b-2 font-medium text-sm border-transparent text-surface-500 hover:text-surface-700 hover:border-surface-300`}>
                        <Box size={18} /> Monitoring Stok
                    </Link>
                </nav>
            </div>

            <PageHeader title="Master Produk" subtitle={`${products.total} produk terdaftar`} actions={canMutate && <LinkButton href="/products/create"><Plus size={16} className="inline mr-1 -mt-0.5"/>Tambah Produk</LinkButton>} />
            <DataTable 
                columns={columns} 
                data={products.data} 
                pagination={products} 
                filters={filters} 
                searchPlaceholder="Cari SKU atau nama..." 
                extraFilters={extraFilters} 
                defaultView="gallery"
                renderGridItem={(r) => {
                    const globalQty = r.inventory_stocks_sum_quantity || 0;
                    const myQty = r.warehouse_stock || 0;
                    const qty = showGlobal ? globalQty : myQty;
                    return (
                        <div className="bg-white rounded-xl border border-surface-200 overflow-hidden flex flex-col h-full hover:shadow-md transition-shadow group">
                            <div className="relative h-48 bg-surface-100 flex-shrink-0">
                                {r.image ? (
                                    <img src={`/storage/${r.image}`} className="w-full h-full object-cover" />
                                ) : (
                                    <div className="w-full h-full flex items-center justify-center text-surface-400">No Image</div>
                                )}
                                <div className="absolute top-2 right-2">
                                    {r.is_active ? <Badge color="success">Aktif</Badge> : <Badge color="danger">Nonaktif</Badge>}
                                </div>
                            </div>
                            <div className="p-4 flex flex-col flex-1">
                                <p className="text-xs text-surface-500 font-mono mb-1">{r.sku}</p>
                                <h3 className="font-bold text-surface-900 leading-tight mb-1 line-clamp-2">{r.name}</h3>
                                <p className="text-xs text-primary-600 mb-3">{r.category?.name}</p>
                                
                                <div className="mt-auto">
                                    <div className="flex justify-between items-end mb-4">
                                        <div>
                                            <p className="text-xs text-surface-500">Harga Jual</p>
                                            <p className="font-semibold text-surface-900">Rp {Number(r.selling_price).toLocaleString('id-ID')}</p>
                                        </div>
                                        <div className="text-right">
                                            <p className="text-xs text-surface-500 flex items-center justify-end gap-1">
                                                {showGlobal ? <Globe size={12}/> : <Building2 size={12}/>} Stok
                                            </p>
                                            <p className={`font-bold ${qty <= r.minimum_stock ? 'text-red-600' : 'text-surface-900'}`}>{qty}</p>
                                        </div>
                                    </div>
                                    
                                    <div className="flex items-center gap-2 pt-3 border-t border-surface-100">
                                        <Link href={`/products/${r.id}`} className="flex-1 py-2 text-center bg-surface-50 hover:bg-surface-100 text-surface-700 rounded-lg text-sm font-medium transition-colors">Detail</Link>
                                        {canMutate && <Link href={`/products/${r.id}/edit`} className="p-2 bg-surface-50 hover:bg-primary-50 text-surface-600 hover:text-primary-600 rounded-lg transition-colors"><Pencil size={16}/></Link>}
                                        {isAdmin && <DeleteButton href={`/products/${r.id}`} name={r.name} />}
                                    </div>
                                </div>
                            </div>
                        </div>
                    );
                }}
            />
        </AuthenticatedLayout>
    );
}
