import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, useForm } from '@inertiajs/react';
import { PageHeader, FormCard, FormField, Input, Textarea } from '@/Components/UI';

export default function Create() {
    const { data, setData, post, processing, errors } = useForm({ name:'',contact_person:'',phone:'',email:'',address:'' });
    return (
        <AuthenticatedLayout title="Tambah Supplier"><Head title="Tambah Supplier" />
            <PageHeader title="Tambah Supplier" />
            <FormCard onSubmit={e=>{e.preventDefault();post('/suppliers');}} processing={processing}>
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
