import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, useForm } from '@inertiajs/react';
import { PageHeader, FormCard, FormField, Input, Select, Textarea } from '@/Components/UI';

export default function CreateIn({ products, warehouses, suppliers }) {
    const { data, setData, post, processing, errors } = useForm({ product_id:'',warehouse_id:'',supplier_id:'',quantity:'',transaction_date:new Date().toISOString().split('T')[0],notes:'' });
    return (
        <AuthenticatedLayout title="Barang Masuk"><Head title="Barang Masuk" />
            <PageHeader title="Barang Masuk" subtitle="Catat penerimaan barang" />
            <FormCard onSubmit={e=>{e.preventDefault();post('/stock-transactions/store-in');}} processing={processing} submitLabel="Simpan Barang Masuk">
                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <FormField label="Produk *" error={errors.product_id}><Select value={data.product_id} onChange={e=>setData('product_id',e.target.value)} options={products.map(p=>({value:p.id,label:`${p.sku} — ${p.name}`}))} placeholder="Pilih produk"/></FormField>
                    <FormField label="Gudang *" error={errors.warehouse_id}><Select value={data.warehouse_id} onChange={e=>setData('warehouse_id',e.target.value)} options={warehouses.map(w=>({value:w.id,label:w.name}))} placeholder="Pilih gudang"/></FormField>
                    <FormField label="Supplier" error={errors.supplier_id}><Select value={data.supplier_id} onChange={e=>setData('supplier_id',e.target.value)} options={suppliers.map(s=>({value:s.id,label:s.name}))} placeholder="Pilih supplier"/></FormField>
                    <FormField label="Jumlah *" error={errors.quantity}><Input type="number" min="1" value={data.quantity} onChange={e=>setData('quantity',e.target.value)}/></FormField>
                    <FormField label="Tanggal *" error={errors.transaction_date}><Input type="date" value={data.transaction_date} onChange={e=>setData('transaction_date',e.target.value)}/></FormField>
                </div>
                <FormField label="Catatan"><Textarea value={data.notes} onChange={e=>setData('notes',e.target.value)}/></FormField>
            </FormCard>
        </AuthenticatedLayout>
    );
}
