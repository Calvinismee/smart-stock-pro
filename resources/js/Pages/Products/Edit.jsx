import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, useForm } from '@inertiajs/react';
import { PageHeader, FormCard, FormField, Input, Select, Textarea } from '@/Components/UI';
import { useState } from 'react';

export default function Edit({ product, categories, suppliers }) {
    const { data, setData, post, processing, errors } = useForm({
        _method: 'PUT', sku: product.sku, name: product.name, category_id: product.category_id||'',
        supplier_id: product.supplier_id||'', description: product.description||'', unit: product.unit,
        purchase_price: product.purchase_price, selling_price: product.selling_price,
        minimum_stock: product.minimum_stock, image: null, is_active: product.is_active,
    });
    const [preview, setPreview] = useState(product.image ? `/storage/${product.image}` : null);

    const handleImage = (e) => {
        const file = e.target.files[0]; setData('image', file);
        if (file) setPreview(URL.createObjectURL(file));
    };

    const submit = (e) => { e.preventDefault(); post(`/products/${product.id}`, { forceFormData: true }); };

    return (
        <AuthenticatedLayout title="Edit Produk">
            <Head title="Edit Produk" />
            <PageHeader title="Edit Produk" subtitle={product.name} />
            <FormCard onSubmit={submit} processing={processing} submitLabel="Perbarui">
                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <FormField label="SKU *" error={errors.sku}><Input value={data.sku} onChange={e=>setData('sku',e.target.value)}/></FormField>
                    <FormField label="Nama *" error={errors.name}><Input value={data.name} onChange={e=>setData('name',e.target.value)}/></FormField>
                    <FormField label="Kategori *" error={errors.category_id}><Select value={data.category_id} onChange={e=>setData('category_id',e.target.value)} options={categories.map(c=>({value:c.id,label:c.name}))} placeholder="Pilih kategori"/></FormField>
                    <FormField label="Supplier"><Select value={data.supplier_id} onChange={e=>setData('supplier_id',e.target.value)} options={suppliers.map(s=>({value:s.id,label:s.name}))} placeholder="Pilih supplier"/></FormField>
                    <FormField label="Satuan *"><Input value={data.unit} onChange={e=>setData('unit',e.target.value)}/></FormField>
                    <FormField label="Stok Minimum *"><Input type="number" value={data.minimum_stock} onChange={e=>setData('minimum_stock',parseInt(e.target.value)||0)}/></FormField>
                    <FormField label="Harga Beli *"><Input type="number" value={data.purchase_price} onChange={e=>setData('purchase_price',e.target.value)}/></FormField>
                    <FormField label="Harga Jual *"><Input type="number" value={data.selling_price} onChange={e=>setData('selling_price',e.target.value)}/></FormField>
                </div>
                <FormField label="Deskripsi"><Textarea value={data.description||''} onChange={e=>setData('description',e.target.value)}/></FormField>
                <FormField label="Gambar Produk" error={errors.image}>
                    <input type="file" accept="image/*" onChange={handleImage} className="text-sm"/>
                    {preview && <img src={preview} className="mt-2 w-32 h-32 object-cover rounded-lg"/>}
                </FormField>
                <FormField label="Status"><label className="flex items-center gap-2"><input type="checkbox" checked={data.is_active} onChange={e=>setData('is_active',e.target.checked)} className="rounded border-surface-300 text-primary-600"/><span className="text-sm">Aktif</span></label></FormField>
            </FormCard>
        </AuthenticatedLayout>
    );
}
