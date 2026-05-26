import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link } from '@inertiajs/react';
import { DataTable, PageHeader, DeleteButton, LinkButton } from '@/Components/UI';
import { Plus, Pencil } from 'lucide-react';

export default function Index({ suppliers, filters }) {
    const columns = [
        { key: 'name', label: 'Nama', sortable: true, render: r => <span className="font-medium text-surface-900">{r.name}</span> },
        { key: 'contact_person', label: 'Kontak', render: r => r.contact_person || '-' },
        { key: 'phone', label: 'Telepon', render: r => r.phone || '-' },
        { key: 'email', label: 'Email', render: r => r.email || '-' },
        { key: 'products_count', label: 'Produk', render: r => r.products_count },
        { key: 'actions', label: 'Aksi', render: r => (
            <div className="flex items-center gap-3">
                <Link href={`/suppliers/${r.id}/edit`} className="text-surface-500 hover:text-primary-600"><Pencil size={16}/></Link>
                <DeleteButton href={`/suppliers/${r.id}`} name={r.name} />
            </div>
        )},
    ];
    return (
        <AuthenticatedLayout title="Supplier"><Head title="Supplier" />
            <PageHeader title="Supplier" subtitle={`${suppliers.total} supplier`} actions={<LinkButton href="/suppliers/create"><Plus size={16} className="inline mr-1 -mt-0.5"/>Tambah</LinkButton>} />
            <DataTable columns={columns} data={suppliers.data} pagination={suppliers} filters={filters} />
        </AuthenticatedLayout>
    );
}
