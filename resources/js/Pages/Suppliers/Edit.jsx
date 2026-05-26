import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, useForm } from '@inertiajs/react';
import { PageHeader, FormCard, FormField, Input, Textarea } from '@/Components/UI';

export default function Edit({ supplier }) {
    const { data, setData, put, processing, errors } = useForm({ name:supplier.name,contact_person:supplier.contact_person||'',phone:supplier.phone||'',email:supplier.email||'',address:supplier.address||'' });
    return (
        <AuthenticatedLayout title="Edit Supplier"><Head title="Edit Supplier" />
            <PageHeader title="Edit Supplier" subtitle={supplier.name} />
            <FormCard onSubmit={e=>{e.preventDefault();put(`/suppliers/${supplier.id}`);}} processing={processing} submitLabel="Perbarui">
                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <FormField label="Nama *" error={errors.name}><Input value={data.name} onChange={e=>setData('name',e.target.value)}/></FormField>
                    <FormField label="Kontak"><Input value={data.contact_person} onChange={e=>setData('contact_person',e.target.value)}/></FormField>
                    <FormField label="Telepon"><Input value={data.phone} onChange={e=>setData('phone',e.target.value)}/></FormField>
                    <FormField label="Email"><Input type="email" value={data.email} onChange={e=>setData('email',e.target.value)}/></FormField>
                </div>
                <FormField label="Alamat"><Textarea value={data.address} onChange={e=>setData('address',e.target.value)}/></FormField>
            </FormCard>
        </AuthenticatedLayout>
    );
}
