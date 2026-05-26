import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, usePage } from '@inertiajs/react';
import { PageHeader, Badge } from '@/Components/UI';
import { useState } from 'react';

export default function Show({ product }) {
    const { auth } = usePage().props;
    const isStaff = auth.user.role === 'staff';
    const userWarehouseId = auth.user.warehouse_id;
    const [showOthers, setShowOthers] = useState(false);

    const myStock = product.inventory_stocks?.find(s => s.warehouse_id === userWarehouseId);
    const otherStocks = product.inventory_stocks?.filter(s => s.warehouse_id !== userWarehouseId) || [];

    const allImages = [];
    if (product.image) allImages.push(`/storage/${product.image}`);
    if (product.gallery && product.gallery.length > 0) {
        product.gallery.forEach(img => allImages.push(`/storage/${img}`));
    }
    const [currentImageIdx, setCurrentImageIdx] = useState(0);

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
                    {allImages.length > 0 && (
                        <div className="relative mb-4 group rounded-xl overflow-hidden shadow-sm border border-surface-200">
                            <img src={allImages[currentImageIdx]} className="w-full h-72 object-cover bg-surface-100 transition-all duration-300"/>
                            {allImages.length > 1 && (
                                <>
                                    <button onClick={() => setCurrentImageIdx(prev => prev === 0 ? allImages.length - 1 : prev - 1)} className="absolute left-3 top-1/2 -translate-y-1/2 bg-black/40 hover:bg-black/60 text-white w-8 h-8 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all">‹</button>
                                    <button onClick={() => setCurrentImageIdx(prev => prev === allImages.length - 1 ? 0 : prev + 1)} className="absolute right-3 top-1/2 -translate-y-1/2 bg-black/40 hover:bg-black/60 text-white w-8 h-8 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all">›</button>
                                    <div className="absolute bottom-3 left-1/2 -translate-x-1/2 flex gap-1.5">
                                        {allImages.map((_, i) => (
                                            <button key={i} onClick={() => setCurrentImageIdx(i)} className={`w-2 h-2 rounded-full transition-all ${i === currentImageIdx ? 'bg-white scale-125' : 'bg-white/50 hover:bg-white/75'}`} />
                                        ))}
                                    </div>
                                </>
                            )}
                        </div>
                    )}
                    <div className="bg-white rounded-xl border border-surface-200 p-5">
                        <h3 className="text-sm font-semibold text-surface-900 mb-3">Stok per Gudang</h3>
                        {isStaff ? (
                            <div className="space-y-4">
                                {myStock ? (
                                    <div className="p-3 bg-primary-50 rounded-lg border border-primary-100">
                                        <p className="text-xs text-primary-600 mb-1 font-semibold">Gudang Anda ({myStock.warehouse?.name})</p>
                                        <div className="flex justify-between items-center text-sm">
                                            <span className="text-surface-900 font-medium">Tersedia</span>
                                            <span className={`font-bold text-lg ${myStock.quantity <= product.minimum_stock ? 'text-red-600' : 'text-primary-700'}`}>{myStock.quantity}</span>
                                        </div>
                                    </div>
                                ) : (
                                    <p className="text-surface-400 text-sm">Belum ada stok di gudang Anda</p>
                                )}
                                
                                {otherStocks.length > 0 && (
                                    <div>
                                        <button onClick={() => setShowOthers(!showOthers)} className="text-xs text-surface-500 hover:text-surface-700 font-medium flex items-center mb-2">
                                            {showOthers ? 'Sembunyikan gudang lain' : 'Lihat stok di gudang lain'}
                                        </button>
                                        {showOthers && (
                                            <div className="space-y-2 mt-2">
                                                {otherStocks.map(s => (
                                                    <div key={s.id} className="flex justify-between items-center text-sm py-1.5 border-b border-surface-100">
                                                        <span className="text-surface-700">{s.warehouse?.name}</span>
                                                        <span className={`font-bold ${s.quantity <= product.minimum_stock ? 'text-red-600' : 'text-surface-900'}`}>{s.quantity}</span>
                                                    </div>
                                                ))}
                                            </div>
                                        )}
                                    </div>
                                )}
                            </div>
                        ) : (
                            product.inventory_stocks?.length > 0 ? (
                                <div className="space-y-2">
                                    {product.inventory_stocks.map(s => (
                                        <div key={s.id} className="flex justify-between items-center text-sm py-1.5 border-b border-surface-100">
                                            <span className="text-surface-700">{s.warehouse?.name}</span>
                                            <span className={`font-bold ${s.quantity <= product.minimum_stock ? 'text-red-600' : 'text-surface-900'}`}>{s.quantity}</span>
                                        </div>
                                    ))}
                                </div>
                            ) : <p className="text-surface-400 text-sm">Belum ada stok</p>
                        )}
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
