import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, useForm } from '@inertiajs/react';
import { DataTable, PageHeader, Badge, FormField, Input, Select, Textarea, Modal } from '@/Components/UI';
import { ArrowDownToLine, ArrowUpFromLine } from 'lucide-react';
import { useState, useEffect } from 'react';

export default function Index({ transactions, warehouses, products, suppliers, filters }) {
    const [modalType, setModalType] = useState(filters.modal || null);

    const formIn = useForm({ product_id: '', warehouse_id: warehouses.length === 1 ? warehouses[0].id : '', quantity: '', transaction_date: new Date().toISOString().split('T')[0], notes: '' });
    const formOut = useForm({ product_id: '', warehouse_id: warehouses.length === 1 ? warehouses[0].id : '', quantity: '', transaction_date: new Date().toISOString().split('T')[0], notes: '' });

    useEffect(() => {
        if (filters.modal) setModalType(filters.modal);
    }, [filters.modal]);

    const submitIn = (e) => {
        e.preventDefault();
        formIn.post('/stock-transactions/store-in', {
            onSuccess: () => { setModalType(null); formIn.reset(); }
        });
    };

    const submitOut = (e) => {
        e.preventDefault();
        formOut.post('/stock-transactions/store-out', {
            onSuccess: () => { setModalType(null); formOut.reset(); }
        });
    };

    const closeModals = () => {
        setModalType(null);
        formIn.clearErrors();
        formOut.clearErrors();
    };

    // Calculate available stock for formOut
    const selectedProductOut = products?.find(p => String(p.id) === String(formOut.data.product_id));
    const availableStockOut = selectedProductOut?.inventory_stocks?.find(s => String(s.warehouse_id) === String(formOut.data.warehouse_id))?.quantity || 0;
    
    // Calculate available stock for formIn
    const selectedProductIn = products?.find(p => String(p.id) === String(formIn.data.product_id));
    const availableStockIn = selectedProductIn?.inventory_stocks?.find(s => String(s.warehouse_id) === String(formIn.data.warehouse_id))?.quantity || 0;

    const columns = [
        { key: 'transaction_code', label: 'Kode', render: r => <span className="font-mono text-xs">{r.transaction_code}</span> },
        { key: 'type', label: 'Tipe', render: r => r.type==='in' ? <Badge color="success">Masuk</Badge> : <Badge color="danger">Keluar</Badge> },
        { key: 'product', label: 'Produk', render: r => <div><p className="font-medium">{r.product?.name}</p><p className="text-xs text-surface-500">{r.product?.sku}</p></div> },
        { key: 'warehouse', label: 'Gudang', render: r => r.warehouse?.name },
        { key: 'quantity', label: 'Qty', sortable: true, render: r => <span className="font-bold">{r.quantity}</span> },
        { key: 'transaction_date', label: 'Tanggal', sortable: true, render: r => new Date(r.transaction_date).toLocaleDateString('id-ID') },
        { key: 'creator', label: 'Dibuat', render: r => r.creator?.name },
    ];

    return (
        <AuthenticatedLayout title="Transaksi Stok">
            <Head title="Transaksi Stok" />
            <PageHeader title="Transaksi Stok (Eksternal)" subtitle={`${transactions.total} riwayat barang masuk & keluar dengan pihak luar`}
                actions={
                    <div className="flex gap-2">
                        <button onClick={() => setModalType('in')} className="px-4 py-2.5 font-medium rounded-xl text-sm transition bg-primary-600 hover:bg-primary-700 text-white">
                            <ArrowDownToLine size={16} className="inline mr-1 -mt-0.5"/>Barang Masuk
                        </button>
                        <button onClick={() => setModalType('out')} className="px-4 py-2.5 font-medium rounded-xl text-sm transition bg-surface-100 hover:bg-surface-200 text-surface-700">
                            <ArrowUpFromLine size={16} className="inline mr-1 -mt-0.5"/>Barang Keluar
                        </button>
                    </div>
                } 
            />
            
            <DataTable columns={columns} data={transactions.data} pagination={transactions} filters={filters} searchPlaceholder="Cari kode atau produk..." />

            <Modal isOpen={modalType === 'in'} onClose={closeModals} title="Barang Masuk (Dari Pihak Luar)">
                <form onSubmit={submitIn} className="space-y-4">
                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <FormField label="Produk *" error={formIn.errors.product_id}><Select value={formIn.data.product_id} onChange={e=>formIn.setData('product_id',e.target.value)} options={products?.map(p=>({value:p.id,label:`${p.sku} — ${p.name}`}))} placeholder="Pilih produk"/></FormField>
                        {warehouses.length > 1 && (
                            <FormField label="Gudang *" error={formIn.errors.warehouse_id}><Select value={formIn.data.warehouse_id} onChange={e=>formIn.setData('warehouse_id',e.target.value)} options={warehouses.map(w=>({value:w.id,label:w.name}))} placeholder="Pilih gudang"/></FormField>
                        )}

                        <FormField label="Jumlah *" error={formIn.errors.quantity}>
                            <Input type="number" min="1" value={formIn.data.quantity} onChange={e=>formIn.setData('quantity',e.target.value)}/>
                            {formIn.data.product_id && formIn.data.warehouse_id && (
                                <p className="text-xs text-surface-500 mt-1">Stok saat ini: {availableStockIn}</p>
                            )}
                        </FormField>
                        <FormField label="Tanggal *" error={formIn.errors.transaction_date}><Input type="date" value={formIn.data.transaction_date} onChange={e=>formIn.setData('transaction_date',e.target.value)}/></FormField>
                    </div>
                    <FormField label="Catatan"><Textarea value={formIn.data.notes} onChange={e=>formIn.setData('notes',e.target.value)}/></FormField>
                    
                    <div className="flex gap-3 mt-6 pt-4 border-t border-surface-200">
                        <button type="submit" disabled={formIn.processing} className="px-6 py-2.5 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-xl transition disabled:opacity-50">
                            {formIn.processing ? 'Menyimpan...' : 'Simpan Barang Masuk'}
                        </button>
                        <button type="button" onClick={closeModals} className="px-6 py-2.5 border border-surface-200 text-surface-700 hover:bg-surface-50 font-medium rounded-xl transition">
                            Batal
                        </button>
                    </div>
                </form>
            </Modal>

            <Modal isOpen={modalType === 'out'} onClose={closeModals} title="Barang Keluar (Ke Pihak Luar)">
                <form onSubmit={submitOut} className="space-y-4">
                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <FormField label="Produk *" error={formOut.errors.product_id}><Select value={formOut.data.product_id} onChange={e=>formOut.setData('product_id',e.target.value)} options={products?.map(p=>({value:p.id,label:`${p.sku} — ${p.name}`}))} placeholder="Pilih produk"/></FormField>
                        {warehouses.length > 1 && (
                            <FormField label="Gudang *" error={formOut.errors.warehouse_id}><Select value={formOut.data.warehouse_id} onChange={e=>formOut.setData('warehouse_id',e.target.value)} options={warehouses.map(w=>({value:w.id,label:w.name}))} placeholder="Pilih gudang"/></FormField>
                        )}
                        <FormField label="Jumlah *" error={formOut.errors.quantity}>
                            <Input type="number" min="1" max={availableStockOut} value={formOut.data.quantity} onChange={e=>formOut.setData('quantity',e.target.value)}/>
                            {formOut.data.product_id && formOut.data.warehouse_id && (
                                <p className="text-xs text-surface-500 mt-1">Stok tersedia: {availableStockOut}</p>
                            )}
                        </FormField>
                        <FormField label="Tanggal *" error={formOut.errors.transaction_date}><Input type="date" value={formOut.data.transaction_date} onChange={e=>formOut.setData('transaction_date',e.target.value)}/></FormField>
                    </div>
                    <FormField label="Catatan"><Textarea value={formOut.data.notes} onChange={e=>formOut.setData('notes',e.target.value)}/></FormField>
                    
                    <div className="flex gap-3 mt-6 pt-4 border-t border-surface-200">
                        <button type="submit" disabled={formOut.processing} className="px-6 py-2.5 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-xl transition disabled:opacity-50">
                            {formOut.processing ? 'Menyimpan...' : 'Simpan Barang Keluar'}
                        </button>
                        <button type="button" onClick={closeModals} className="px-6 py-2.5 border border-surface-200 text-surface-700 hover:bg-surface-50 font-medium rounded-xl transition">
                            Batal
                        </button>
                    </div>
                </form>
            </Modal>
        </AuthenticatedLayout>
    );
}
