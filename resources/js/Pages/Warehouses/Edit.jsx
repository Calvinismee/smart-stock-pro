import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, useForm } from '@inertiajs/react';
import { PageHeader, FormCard, FormField, Input, Textarea } from '@/Components/UI';

export default function Edit({ warehouse }) {
    const { data, setData, put, processing, errors } = useForm({ code:warehouse.code,name:warehouse.name,city:warehouse.city,address:warehouse.address||'',latitude:warehouse.latitude||'',longitude:warehouse.longitude||'',phone:warehouse.phone||'',is_active:warehouse.is_active });
    return (
        <AuthenticatedLayout title="Edit Gudang"><Head title="Edit Gudang" />
            <PageHeader title="Edit Gudang" subtitle={warehouse.name} />
            <FormCard onSubmit={e=>{e.preventDefault();put(`/warehouses/${warehouse.id}`);}} processing={processing} submitLabel="Perbarui">
                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <FormField label="Kode *" error={errors.code}><Input value={data.code} onChange={e=>setData('code',e.target.value)}/></FormField>
                    <FormField label="Nama *" error={errors.name}><Input value={data.name} onChange={e=>setData('name',e.target.value)}/></FormField>
                    <FormField label="Kota *" error={errors.city}><Input value={data.city} onChange={e=>setData('city',e.target.value)}/></FormField>
                    <FormField label="Telepon"><Input value={data.phone} onChange={e=>setData('phone',e.target.value)}/></FormField>
                    <FormField label="Latitude"><Input type="number" step="any" value={data.latitude} onChange={e=>setData('latitude',e.target.value)}/></FormField>
                    <FormField label="Longitude"><Input type="number" step="any" value={data.longitude} onChange={e=>setData('longitude',e.target.value)}/></FormField>
                </div>
                <FormField label="Alamat"><Textarea value={data.address} onChange={e=>setData('address',e.target.value)}/></FormField>
                <FormField label="Status"><label className="flex items-center gap-2"><input type="checkbox" checked={data.is_active} onChange={e=>setData('is_active',e.target.checked)} className="rounded border-surface-300 text-primary-600"/><span className="text-sm">Aktif</span></label></FormField>
            </FormCard>
        </AuthenticatedLayout>
    );
}
