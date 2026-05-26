import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link } from '@inertiajs/react';
import { DataTable, PageHeader, Badge, DeleteButton, LinkButton } from '@/Components/UI';
import { Plus, Eye, Pencil } from 'lucide-react';

export default function Index({ warehouses, filters }) {
    const columns = [
        { key: 'code', label: 'Kode', sortable: true, render: r => <span className="font-mono text-xs">{r.code}</span> },
        { key: 'name', label: 'Nama', sortable: true, render: r => <div><p className="font-medium text-surface-900">{r.name}</p><p className="text-xs text-surface-500">{r.city}</p></div> },
        { key: 'phone', label: 'Telepon', render: r => r.phone || '-' },
        { key: 'inventory_stocks_sum_quantity', label: 'Total Stok', render: r => (r.inventory_stocks_sum_quantity || 0).toLocaleString('id-ID') },
        { key: 'is_active', label: 'Status', render: r => r.is_active ? <Badge color="success">Aktif</Badge> : <Badge color="danger">Nonaktif</Badge> },
        { key: 'actions', label: 'Aksi', render: r => (
            <div className="flex items-center gap-3">
                <Link href={`/warehouses/${r.id}`} className="text-surface-500 hover:text-primary-600"><Eye size={16}/></Link>
                <Link href={`/warehouses/${r.id}/edit`} className="text-surface-500 hover:text-primary-600"><Pencil size={16}/></Link>
                <DeleteButton href={`/warehouses/${r.id}`} name={r.name} />
            </div>
        )},
    ];
    return (
        <AuthenticatedLayout title="Gudang"><Head title="Gudang" />
            <PageHeader title="Gudang" subtitle={`${warehouses.total} gudang`} actions={<LinkButton href="/warehouses/create"><Plus size={16} className="inline mr-1 -mt-0.5"/>Tambah</LinkButton>} />
            <DataTable columns={columns} data={warehouses.data} pagination={warehouses} filters={filters} />
        </AuthenticatedLayout>
    );
}
