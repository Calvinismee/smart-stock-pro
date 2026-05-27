import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, useForm } from '@inertiajs/react';
import { PageHeader, FormField, Input, Select, Textarea } from '@/Components/UI';
import { useState } from 'react';
import { UploadCloud, Image as ImageIcon } from 'lucide-react';

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
            <form onSubmit={submit} className="max-w-4xl space-y-6 pb-12">
                {/* Basic Info */}
                <div className="bg-white rounded-xl border border-surface-200 p-6 shadow-sm">
                    <h2 className="text-lg font-semibold text-surface-900 mb-4 border-b border-surface-100 pb-3">Informasi Dasar</h2>
                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <FormField label="Nama Produk *" error={errors.name}><Input value={data.name} onChange={e=>setData('name',e.target.value)} placeholder="Contoh: iPhone 15 Pro" error={errors.name}/></FormField>
                        <FormField label="SKU *" error={errors.sku}><Input value={data.sku} onChange={e=>setData('sku',e.target.value)} placeholder="Contoh: IPH-15-PRO-128" error={errors.sku}/></FormField>
                        
                        <FormField label="Kategori *" error={errors.category_id}><Select value={data.category_id} onChange={e=>setData('category_id',e.target.value)} options={categories.map(c=>({value:c.id,label:c.name}))} placeholder="Pilih kategori" error={errors.category_id}/></FormField>
                        <FormField label="Supplier" error={errors.supplier_id}><Select value={data.supplier_id} onChange={e=>setData('supplier_id',e.target.value)} options={suppliers.map(s=>({value:s.id,label:s.name}))} placeholder="Pilih supplier"/></FormField>
                    </div>
                    
                    <div className="mt-5">
                        <FormField label="Deskripsi Produk" error={errors.description}><Textarea value={data.description||''} onChange={e=>setData('description',e.target.value)} placeholder="Jelaskan spesifikasi dan detail produk di sini..."/></FormField>
                    </div>
                </div>

                {/* Price & Stock */}
                <div className="bg-white rounded-xl border border-surface-200 p-6 shadow-sm">
                    <h2 className="text-lg font-semibold text-surface-900 mb-4 border-b border-surface-100 pb-3">Harga & Stok</h2>
                    <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                        <FormField label="Satuan *" error={errors.unit}><Input value={data.unit} onChange={e=>setData('unit',e.target.value)} placeholder="pcs, box, kg..."/></FormField>
                        <FormField label="Batas Stok Minimum *" error={errors.minimum_stock}><Input type="number" min="0" value={data.minimum_stock} onChange={e=>setData('minimum_stock',parseInt(e.target.value)||0)}/></FormField>
                        <FormField label="Harga Beli (Rp) *" error={errors.purchase_price}><Input type="number" min="0" value={data.purchase_price} onChange={e=>setData('purchase_price',e.target.value)} placeholder="0"/></FormField>
                        <FormField label="Harga Jual (Rp) *" error={errors.selling_price}><Input type="number" min="0" value={data.selling_price} onChange={e=>setData('selling_price',e.target.value)} placeholder="0"/></FormField>
                    </div>
                </div>

                {/* Media */}
                <div className="bg-white rounded-xl border border-surface-200 p-6 shadow-sm">
                    <h2 className="text-lg font-semibold text-surface-900 mb-4 border-b border-surface-100 pb-3">Media Gambar</h2>
                    <div className="grid grid-cols-1 sm:grid-cols-3 gap-6">
                        <div className="sm:col-span-1">
                            <FormField label="Gambar Utama" error={errors.image}>
                                <label className="flex flex-col items-center justify-center w-full h-48 border-2 border-surface-200 border-dashed rounded-xl cursor-pointer bg-surface-50 hover:bg-surface-100 transition relative overflow-hidden group">
                                    {preview ? (
                                        <>
                                            <img src={preview} className="absolute inset-0 w-full h-full object-cover"/>
                                            <div className="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition text-white font-medium">Ganti Gambar</div>
                                        </>
                                    ) : (
                                        <div className="flex flex-col items-center justify-center pt-5 pb-6 text-surface-500">
                                            <ImageIcon size={32} className="mb-2 text-surface-400"/>
                                            <p className="text-sm font-medium">Klik untuk upload</p>
                                            <p className="text-xs mt-1">PNG, JPG up to 2MB</p>
                                        </div>
                                    )}
                                    <input type="file" accept="image/*" className="hidden" onChange={handleImage} />
                                </label>
                            </FormField>
                        </div>
                        
                        <div className="sm:col-span-2">
                            <FormField label="Galeri Gambar Tambahan (Bisa lebih dari 1)" error={errors.gallery}>
                                <label className="flex flex-col items-center justify-center w-full h-32 border-2 border-surface-200 border-dashed rounded-xl cursor-pointer bg-surface-50 hover:bg-surface-100 transition relative">
                                    <div className="flex flex-col items-center justify-center text-surface-500">
                                        <UploadCloud size={28} className="mb-2 text-surface-400"/>
                                        <p className="text-sm font-medium">Upload banyak gambar sekaligus</p>
                                    </div>
                                    <input type="file" accept="image/*" multiple className="hidden" onChange={handleGallery} />
                                </label>
                                
                                {galleryPreviews.length > 0 && (
                                    <div className="flex gap-3 flex-wrap mt-4">
                                        {galleryPreviews.map((src, i) => (
                                            <div key={i} className="relative w-20 h-20 rounded-lg overflow-hidden border border-surface-200">
                                                <img src={src} className="w-full h-full object-cover"/>
                                            </div>
                                        ))}
                                    </div>
                                )}
                            </FormField>
                        </div>
                    </div>
                </div>

                <div className="flex items-center justify-between bg-white rounded-xl border border-surface-200 p-6 shadow-sm">
                    <FormField label="">
                        <label className="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" checked={data.is_active} onChange={e=>setData('is_active',e.target.checked)} className="w-5 h-5 rounded border-surface-300 text-primary-600 focus:ring-primary-500" />
                            <span className="font-medium text-surface-900">Produk Aktif</span>
                            <span className="text-surface-500 text-sm ml-2 hidden sm:inline">(Produk bisa digunakan untuk transaksi)</span>
                        </label>
                    </FormField>
                    
                    <div className="flex gap-3">
                        <button type="button" onClick={() => window.history.back()} className="px-6 py-2.5 border border-surface-200 text-surface-700 hover:bg-surface-50 font-medium rounded-xl transition">
                            Batal
                        </button>
                        <button type="submit" disabled={processing} className="px-8 py-2.5 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-xl transition disabled:opacity-50">
                            {processing ? 'Menyimpan...' : 'Simpan Produk'}
                        </button>
                    </div>
                </div>
            </form>
        </AuthenticatedLayout>
    );
}
