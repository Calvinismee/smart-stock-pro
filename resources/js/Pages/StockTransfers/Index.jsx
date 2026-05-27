import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, useForm, usePage, router } from '@inertiajs/react';
import { DataTable, PageHeader, Badge, FormField, Input, Select, Textarea, Modal } from '@/Components/UI';
import { Plus, Eye, CheckCircle2 } from 'lucide-react';
import { useState, useEffect } from 'react';

export default function Index({ transfers, products, sourceWarehouses, destinationWarehouses, filters }) {
    const { auth } = usePage().props;
    const [isModalOpen, setIsModalOpen] = useState(filters.modal === 'true');
    const [receiveModalId, setReceiveModalId] = useState(null);

    const form = useForm({
        product_id: '',
        source_warehouse_id: sourceWarehouses?.length === 1 ? sourceWarehouses[0].id : '',
        destination_warehouse_id: '',
        quantity: '',
        transfer_date: new Date().toISOString().split('T')[0],
        notes: ''
    });

    useEffect(() => {
        if (filters.modal === 'true') setIsModalOpen(true);
    }, [filters.modal]);

    const submit = (e) => {
        e.preventDefault();
        form.post('/stock-transfers', {
            onSuccess: () => {
                setIsModalOpen(false);
                form.reset();
            }
        });
    };

    const closeModal = () => {
        setIsModalOpen(false);
        form.clearErrors();
    };

    // Calculate available stock for transfer
    const selectedProduct = products?.find(p => String(p.id) === String(form.data.product_id));
    const availableStock = selectedProduct?.inventory_stocks?.find(s => String(s.warehouse_id) === String(form.data.source_warehouse_id))?.quantity || 0;

    const handleReceive = (id) => {
        setReceiveModalId(id);
    };

    const confirmReceive = () => {
        if (receiveModalId) {
            router.post(`/stock-transfers/${receiveModalId}/receive`, {}, {
                onSuccess: () => setReceiveModalId(null)
            });
        }
    };

    const columns = [
        { key: 'transfer_code', label: 'Kode', render: r => <span className="font-mono text-xs">{r.transfer_code}</span> },
        { key: 'product', label: 'Produk', render: r => <div><p className="font-medium">{r.product?.name}</p><p className="text-xs text-surface-500">{r.product?.sku}</p></div> },
        { key: 'route', label: 'Rute', render: r => <span className="text-sm">{r.source_warehouse?.name} → {r.destination_warehouse?.name}</span> },
        { key: 'quantity', label: 'Qty', render: r => <span className="font-bold">{r.quantity}</span> },
        { key: 'transfer_date', label: 'Tanggal', sortable: true, render: r => new Date(r.transfer_date).toLocaleDateString('id-ID') },
        { key: 'status', label: 'Status', render: r => <Badge color={r.status==='completed'?'success':r.status==='in_transit'?'warning':'danger'}>{r.status === 'in_transit' ? 'In Transit' : r.status}</Badge> },
        { key: 'actions', label: '', render: r => (
            <div className="flex gap-2 justify-end">
                {r.status === 'in_transit' && (['admin', 'manager'].includes(auth.user.role) || (auth.user.role === 'staff' && auth.user.warehouse_id === r.destination_warehouse_id)) && (
                    <button onClick={() => handleReceive(r.id)} className="text-primary-600 hover:text-primary-800" title="Terima Barang">
                        <CheckCircle2 size={16} />
                    </button>
                )}
                <Link href={`/stock-transfers/${r.id}`} className="text-surface-500 hover:text-primary-600"><Eye size={16}/></Link>
            </div>
        )},
    ];

    return (
        <AuthenticatedLayout title="Transfer Antar Gudang">
            <Head title="Transfer Antar Gudang" />
            <PageHeader title="Transfer Antar Gudang" subtitle={`${transfers.total} riwayat perpindahan internal antar gudang`} actions={
                <button onClick={() => setIsModalOpen(true)} className="px-4 py-2.5 font-medium rounded-xl text-sm transition bg-primary-600 hover:bg-primary-700 text-white">
                    <Plus size={16} className="inline mr-1 -mt-0.5"/>Buat Transfer
                </button>
            } />
            <DataTable columns={columns} data={transfers.data} pagination={transfers} filters={filters} searchPlaceholder="Cari kode transfer..." />

            <Modal isOpen={isModalOpen} onClose={closeModal} title="Buat Transfer Internal">
                <form onSubmit={submit} className="space-y-4">
                    <FormField label="Produk *" error={form.errors.product_id}>
                        <Select value={form.data.product_id} onChange={e=>form.setData('product_id',e.target.value)} options={products?.map(p=>({value:p.id,label:`${p.sku} — ${p.name}`}))} placeholder="Pilih produk"/>
                    </FormField>
                    
                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        {sourceWarehouses?.length > 1 && (
                            <FormField label="Gudang Asal *" error={form.errors.source_warehouse_id}>
                                <Select value={form.data.source_warehouse_id} onChange={e=>form.setData('source_warehouse_id',e.target.value)} options={sourceWarehouses.map(w=>({value:w.id,label:w.name}))} placeholder="Pilih gudang asal"/>
                            </FormField>
                        )}
                        <FormField label="Gudang Tujuan *" error={form.errors.destination_warehouse_id}>
                            <Select value={form.data.destination_warehouse_id} onChange={e=>form.setData('destination_warehouse_id',e.target.value)} options={destinationWarehouses?.filter(w=>w.id!=form.data.source_warehouse_id).map(w=>({value:w.id,label:w.name}))} placeholder="Pilih gudang tujuan"/>
                        </FormField>
                        <FormField label="Jumlah *" error={form.errors.quantity}>
                            <Input type="number" min="1" max={availableStock} value={form.data.quantity} onChange={e=>form.setData('quantity',e.target.value)}/>
                            {form.data.product_id && form.data.source_warehouse_id && (
                                <p className="text-xs text-surface-500 mt-1">Stok di gudang asal: {availableStock}</p>
                            )}
                        </FormField>
                        <FormField label="Tanggal *" error={form.errors.transfer_date}>
                            <Input type="date" value={form.data.transfer_date} onChange={e=>form.setData('transfer_date',e.target.value)}/>
                        </FormField>
                    </div>
                    
                    <FormField label="Catatan">
                        <Textarea value={form.data.notes} onChange={e=>form.setData('notes',e.target.value)}/>
                    </FormField>
                    
                    <div className="flex gap-3 mt-6 pt-4 border-t border-surface-200">
                        <button type="submit" disabled={form.processing} className="px-6 py-2.5 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-xl transition disabled:opacity-50">
                            {form.processing ? 'Menyimpan...' : 'Proses Transfer'}
                        </button>
                        <button type="button" onClick={closeModal} className="px-6 py-2.5 border border-surface-200 text-surface-700 hover:bg-surface-50 font-medium rounded-xl transition">
                            Batal
                        </button>
                    </div>
                </form>
            </Modal>

            {/* Receive Confirmation Modal */}
            <Modal isOpen={receiveModalId !== null} onClose={() => setReceiveModalId(null)} title="Konfirmasi Terima Barang">
                <div className="p-2">
                    <p className="text-surface-600 mb-6">
                        Apakah Anda yakin ingin menerima barang ini? Proses ini akan secara permanen menambahkan stok ke gudang tujuan Anda dan mengubah status transfer menjadi <span className="font-semibold text-green-600">Selesai (Completed)</span>.
                    </p>
                    <div className="flex gap-3 justify-end pt-4 border-t border-surface-200">
                        <button type="button" onClick={() => setReceiveModalId(null)} className="px-5 py-2.5 border border-surface-200 text-surface-700 hover:bg-surface-50 font-medium rounded-xl transition">
                            Batal
                        </button>
                        <button type="button" onClick={confirmReceive} className="px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white font-medium rounded-xl transition">
                            Ya, Terima Barang
                        </button>
                    </div>
                </div>
            </Modal>
        </AuthenticatedLayout>
    );
}
