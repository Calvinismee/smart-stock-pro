import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, useForm } from '@inertiajs/react';
import { PageHeader, FormCard, FormField, Input, Select, Textarea } from '@/Components/UI';
import { useState } from 'react';

export default function Create({ categories, suppliers }) {
    const { data, setData, post, processing, errors } = useForm({
        sku:'', name:'', category_id:'', supplier_id:'', description:'', unit:'pcs',
        purchase_price:'', selling_price:'', minimum_stock:10, image:null, gallery:[], is_active:true,
    });
    const [preview, setPreview] = useState(null);
    const [galleryPreviews, setGalleryPreviews] = useState([]);

    const handleImage = (e) => {
        const file = e.target.files[0];
        setData('image', file);
        if (file) setPreview(URL.createObjectURL(file));
    };

    const handleGallery = (e) => {
        const files = Array.from(e.target.files);
        setData('gallery', files);
        setGalleryPreviews(files.map(file => URL.createObjectURL(file)));
    };

    const submit = (e) => { e.preventDefault(); post('/products', { forceFormData: true }); };

    return (
        <AuthenticatedLayout title="Tambah Produk">
            <Head title="Tambah Produk" />
            <PageHeader title="Tambah Produk" />
            <FormCard onSubmit={submit} processing={processing}>
                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <FormField label="SKU *" error={errors.sku}><Input value={data.sku} onChange={e=>setData('sku',e.target.value)} error={errors.sku}/></FormField>
                    <FormField label="Nama *" error={errors.name}><Input value={data.name} onChange={e=>setData('name',e.target.value)} error={errors.name}/></FormField>
                    <FormField label="Kategori *" error={errors.category_id}><Select value={data.category_id} onChange={e=>setData('category_id',e.target.value)} options={categories.map(c=>({value:c.id,label:c.name}))} placeholder="Pilih kategori" error={errors.category_id}/></FormField>
                    <FormField label="Supplier" error={errors.supplier_id}><Select value={data.supplier_id} onChange={e=>setData('supplier_id',e.target.value)} options={suppliers.map(s=>({value:s.id,label:s.name}))} placeholder="Pilih supplier"/></FormField>
                    <FormField label="Satuan *" error={errors.unit}><Input value={data.unit} onChange={e=>setData('unit',e.target.value)}/></FormField>
                    <FormField label="Stok Minimum *" error={errors.minimum_stock}><Input type="number" value={data.minimum_stock} onChange={e=>setData('minimum_stock',parseInt(e.target.value)||0)}/></FormField>
                    <FormField label="Harga Beli *" error={errors.purchase_price}><Input type="number" value={data.purchase_price} onChange={e=>setData('purchase_price',e.target.value)}/></FormField>
                    <FormField label="Harga Jual *" error={errors.selling_price}><Input type="number" value={data.selling_price} onChange={e=>setData('selling_price',e.target.value)}/></FormField>
                </div>
                <FormField label="Deskripsi" error={errors.description}><Textarea value={data.description||''} onChange={e=>setData('description',e.target.value)}/></FormField>
                <FormField label="Gambar Utama" error={errors.image}>
                    <input type="file" accept="image/*" onChange={handleImage} className="text-sm" />
                    {preview && <img src={preview} className="mt-2 w-32 h-32 object-cover rounded-lg"/>}
                </FormField>
                <FormField label="Galeri Gambar (Bisa pilih lebih dari satu)" error={errors.gallery}>
                    <input type="file" accept="image/*" multiple onChange={handleGallery} className="text-sm" />
                    {galleryPreviews.length > 0 && (
                        <div className="flex gap-2 flex-wrap mt-2">
                            {galleryPreviews.map((src, i) => (
                                <img key={i} src={src} className="w-20 h-20 object-cover rounded-lg"/>
                            ))}
                        </div>
                    )}
                </FormField>
                <FormField label="Status">
                    <label className="flex items-center gap-2"><input type="checkbox" checked={data.is_active} onChange={e=>setData('is_active',e.target.checked)} className="rounded border-surface-300 text-primary-600" /><span className="text-sm">Aktif</span></label>
                </FormField>
            </FormCard>
        </AuthenticatedLayout>
    );
}
