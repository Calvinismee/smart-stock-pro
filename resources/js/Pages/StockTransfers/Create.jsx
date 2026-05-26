import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, useForm } from '@inertiajs/react';
import { PageHeader, FormCard, FormField, Input, Select, Textarea } from '@/Components/UI';

export default function Create({ products, warehouses }) {
    const { data, setData, post, processing, errors } = useForm({ product_id:'',source_warehouse_id:'',destination_warehouse_id:'',quantity:'',transfer_date:new Date().toISOString().split('T')[0],notes:'' });
    return (
        <AuthenticatedLayout title="Buat Transfer"><Head title="Buat Transfer" />
            <PageHeader title="Transfer Gudang" subtitle="Pindahkan stok antar gudang" />
            <FormCard onSubmit={e=>{e.preventDefault();post('/stock-transfers');}} processing={processing} submitLabel="Proses Transfer">
                <FormField label="Produk *" error={errors.product_id}><Select value={data.product_id} onChange={e=>setData('product_id',e.target.value)} options={products.map(p=>({value:p.id,label:`${p.sku} — ${p.name}`}))} placeholder="Pilih produk"/></FormField>
                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <FormField label="Gudang Asal *" error={errors.source_warehouse_id}><Select value={data.source_warehouse_id} onChange={e=>setData('source_warehouse_id',e.target.value)} options={warehouses.map(w=>({value:w.id,label:w.name}))} placeholder="Pilih gudang asal"/></FormField>
                    <FormField label="Gudang Tujuan *" error={errors.destination_warehouse_id}><Select value={data.destination_warehouse_id} onChange={e=>setData('destination_warehouse_id',e.target.value)} options={warehouses.filter(w=>w.id!=data.source_warehouse_id).map(w=>({value:w.id,label:w.name}))} placeholder="Pilih gudang tujuan"/></FormField>
                    <FormField label="Jumlah *" error={errors.quantity}><Input type="number" min="1" value={data.quantity} onChange={e=>setData('quantity',e.target.value)}/></FormField>
                    <FormField label="Tanggal *" error={errors.transfer_date}><Input type="date" value={data.transfer_date} onChange={e=>setData('transfer_date',e.target.value)}/></FormField>
                </div>
                <FormField label="Catatan"><Textarea value={data.notes} onChange={e=>setData('notes',e.target.value)}/></FormField>
            </FormCard>
        </AuthenticatedLayout>
    );
}
