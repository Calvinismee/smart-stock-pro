import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, useForm } from '@inertiajs/react';
import { PageHeader, FormCard, FormField, Input, Select, Textarea } from '@/Components/UI';

export default function CreateOut({ products, warehouses }) {
    const { data, setData, post, processing, errors } = useForm({ product_id:'',warehouse_id:'',quantity:'',transaction_date:new Date().toISOString().split('T')[0],notes:'' });
    return (
        <AuthenticatedLayout title="Barang Keluar"><Head title="Barang Keluar" />
            <PageHeader title="Barang Keluar" subtitle="Catat pengeluaran barang" />
            <FormCard onSubmit={e=>{e.preventDefault();post('/stock-transactions/store-out');}} processing={processing} submitLabel="Simpan Barang Keluar">
                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <FormField label="Produk *" error={errors.product_id}><Select value={data.product_id} onChange={e=>setData('product_id',e.target.value)} options={products.map(p=>({value:p.id,label:`${p.sku} — ${p.name}`}))} placeholder="Pilih produk"/></FormField>
                    <FormField label="Gudang *" error={errors.warehouse_id}><Select value={data.warehouse_id} onChange={e=>setData('warehouse_id',e.target.value)} options={warehouses.map(w=>({value:w.id,label:w.name}))} placeholder="Pilih gudang"/></FormField>
                    <FormField label="Jumlah *" error={errors.quantity}><Input type="number" min="1" value={data.quantity} onChange={e=>setData('quantity',e.target.value)}/></FormField>
                    <FormField label="Tanggal *" error={errors.transaction_date}><Input type="date" value={data.transaction_date} onChange={e=>setData('transaction_date',e.target.value)}/></FormField>
                </div>
                <FormField label="Catatan"><Textarea value={data.notes} onChange={e=>setData('notes',e.target.value)}/></FormField>
            </FormCard>
        </AuthenticatedLayout>
    );
}
