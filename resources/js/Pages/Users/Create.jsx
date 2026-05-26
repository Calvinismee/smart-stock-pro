import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, useForm } from '@inertiajs/react';
import { PageHeader, FormCard, FormField, Input, Select } from '@/Components/UI';

export default function Create({ warehouses }) {
    const { data, setData, post, processing, errors } = useForm({ name:'',email:'',password:'',password_confirmation:'',role:'auditor',warehouse_id:'',is_active:true });
    return (
        <AuthenticatedLayout title="Tambah User"><Head title="Tambah User" />
            <PageHeader title="Tambah User" />
            <FormCard onSubmit={e=>{e.preventDefault();post('/users');}} processing={processing}>
                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <FormField label="Nama *" error={errors.name}><Input value={data.name} onChange={e=>setData('name',e.target.value)}/></FormField>
                    <FormField label="Email *" error={errors.email}><Input type="email" value={data.email} onChange={e=>setData('email',e.target.value)}/></FormField>
                    <FormField label="Password *" error={errors.password}><Input type="password" value={data.password} onChange={e=>setData('password',e.target.value)}/></FormField>
                    <FormField label="Konfirmasi Password *"><Input type="password" value={data.password_confirmation} onChange={e=>setData('password_confirmation',e.target.value)}/></FormField>
                    <FormField label="Role *" error={errors.role}><Select value={data.role} onChange={e=>setData('role',e.target.value)} options={[{value:'admin',label:'Admin'},{value:'manager',label:'Manager'},{value:'staff',label:'Staff'},{value:'auditor',label:'Auditor'}]}/></FormField>
                    <FormField label="Gudang"><Select value={data.warehouse_id} onChange={e=>setData('warehouse_id',e.target.value)} options={warehouses.map(w=>({value:w.id,label:w.name}))} placeholder="Pilih gudang (opsional)"/></FormField>
                </div>
                <FormField label="Status"><label className="flex items-center gap-2"><input type="checkbox" checked={data.is_active} onChange={e=>setData('is_active',e.target.checked)} className="rounded border-surface-300 text-primary-600"/><span className="text-sm">Aktif</span></label></FormField>
            </FormCard>
        </AuthenticatedLayout>
    );
}
