import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';
import { PageHeader, Badge } from '@/Components/UI';

export default function Show({ product }) {
    return (
        <AuthenticatedLayout title={product.name}>
            <Head title={product.name} />
            <PageHeader title={product.name} subtitle={`SKU: ${product.sku}`} />
            <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div className="lg:col-span-2 bg-white rounded-xl border border-surface-200 p-6 space-y-4">
                    <div className="grid grid-cols-2 gap-4 text-sm">
                        <div><span className="text-surface-500">Kategori</span><p className="font-medium">{product.category?.name}</p></div>
                        <div><span className="text-surface-500">Supplier</span><p className="font-medium">{product.supplier?.name || '-'}</p></div>
                        <div><span className="text-surface-500">Satuan</span><p className="font-medium">{product.unit}</p></div>
                        <div><span className="text-surface-500">Status</span><p>{product.is_active ? <Badge color="success">Aktif</Badge> : <Badge color="danger">Nonaktif</Badge>}</p></div>
                        <div><span className="text-surface-500">Harga Beli</span><p className="font-medium">Rp {Number(product.purchase_price).toLocaleString('id-ID')}</p></div>
                        <div><span className="text-surface-500">Harga Jual</span><p className="font-medium">Rp {Number(product.selling_price).toLocaleString('id-ID')}</p></div>
                        <div><span className="text-surface-500">Stok Minimum</span><p className="font-medium">{product.minimum_stock}</p></div>
                    </div>
                    {product.description && <div><span className="text-surface-500 text-sm">Deskripsi</span><p className="text-sm mt-1">{product.description}</p></div>}
                </div>
                <div>
                    {product.image && <img src={`/storage/${product.image}`} className="rounded-xl w-full object-cover mb-4"/>}
                    <div className="bg-white rounded-xl border border-surface-200 p-5">
                        <h3 className="text-sm font-semibold text-surface-900 mb-3">Stok per Gudang</h3>
                        {product.inventory_stocks?.length > 0 ? (
                            <div className="space-y-2">
                                {product.inventory_stocks.map(s => (
                                    <div key={s.id} className="flex justify-between items-center text-sm py-1.5 border-b border-surface-100">
                                        <span className="text-surface-700">{s.warehouse?.name}</span>
                                        <span className={`font-bold ${s.quantity <= product.minimum_stock ? 'text-red-600' : 'text-surface-900'}`}>{s.quantity}</span>
                                    </div>
                                ))}
                            </div>
                        ) : <p className="text-surface-400 text-sm">Belum ada stok</p>}
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
