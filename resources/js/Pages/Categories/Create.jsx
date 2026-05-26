import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, useForm } from '@inertiajs/react';
import { PageHeader, FormCard, FormField, Input, Textarea } from '@/Components/UI';

export default function Create() {
    const { data, setData, post, processing, errors } = useForm({ name: '', description: '' });
    return (
        <AuthenticatedLayout title="Tambah Kategori"><Head title="Tambah Kategori" />
            <PageHeader title="Tambah Kategori" />
            <FormCard onSubmit={e => { e.preventDefault(); post('/categories'); }} processing={processing}>
                <FormField label="Nama *" error={errors.name}><Input value={data.name} onChange={e=>setData('name',e.target.value)} error={errors.name}/></FormField>
                <FormField label="Deskripsi"><Textarea value={data.description} onChange={e=>setData('description',e.target.value)}/></FormField>
            </FormCard>
        </AuthenticatedLayout>
    );
}
