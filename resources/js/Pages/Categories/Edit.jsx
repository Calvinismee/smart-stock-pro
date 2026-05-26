import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, useForm } from '@inertiajs/react';
import { PageHeader, FormCard, FormField, Input, Textarea } from '@/Components/UI';

export default function Edit({ category }) {
    const { data, setData, put, processing, errors } = useForm({ name: category.name, description: category.description || '' });
    return (
        <AuthenticatedLayout title="Edit Kategori"><Head title="Edit Kategori" />
            <PageHeader title="Edit Kategori" subtitle={category.name} />
            <FormCard onSubmit={e => { e.preventDefault(); put(`/categories/${category.id}`); }} processing={processing} submitLabel="Perbarui">
                <FormField label="Nama *" error={errors.name}><Input value={data.name} onChange={e=>setData('name',e.target.value)}/></FormField>
                <FormField label="Deskripsi"><Textarea value={data.description} onChange={e=>setData('description',e.target.value)}/></FormField>
            </FormCard>
        </AuthenticatedLayout>
    );
}
