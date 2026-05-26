import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link } from '@inertiajs/react';
import { DataTable, PageHeader, DeleteButton, LinkButton } from '@/Components/UI';
import { Plus, Pencil } from 'lucide-react';

export default function Index({ categories, filters }) {
    const columns = [
        { key: 'name', label: 'Nama', sortable: true, render: r => <span className="font-medium text-surface-900">{r.name}</span> },
        { key: 'description', label: 'Deskripsi', render: r => <span className="text-surface-500 text-sm">{r.description || '-'}</span> },
        { key: 'products_count', label: 'Jumlah Produk', render: r => <span>{r.products_count} produk</span> },
        { key: 'actions', label: 'Aksi', render: r => (
            <div className="flex items-center gap-3">
                <Link href={`/categories/${r.id}/edit`} className="text-surface-500 hover:text-primary-600"><Pencil size={16}/></Link>
                <DeleteButton href={`/categories/${r.id}`} name={r.name} />
            </div>
        )},
    ];
    return (
        <AuthenticatedLayout title="Kategori">
            <Head title="Kategori" />
            <PageHeader title="Kategori" subtitle={`${categories.total} kategori`} actions={<LinkButton href="/categories/create"><Plus size={16} className="inline mr-1 -mt-0.5"/>Tambah</LinkButton>} />
            <DataTable columns={columns} data={categories.data} pagination={categories} filters={filters} />
        </AuthenticatedLayout>
    );
}
