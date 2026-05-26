import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link } from '@inertiajs/react';
import { DataTable, PageHeader, Badge, DeleteButton, LinkButton } from '@/Components/UI';
import { Plus, Pencil } from 'lucide-react';

const roleLabels = { admin:'Admin', manager:'Manager', staff:'Staff', viewer:'Viewer' };
const roleColors = { admin:'primary', manager:'info', staff:'success', viewer:'warning' };

export default function Index({ users, filters }) {
    const columns = [
        { key: 'name', label: 'Nama', sortable: true, render: r => <div><p className="font-medium text-surface-900">{r.name}</p><p className="text-xs text-surface-500">{r.email}</p></div> },
        { key: 'role', label: 'Role', render: r => <Badge color={roleColors[r.role]}>{roleLabels[r.role]}</Badge> },
        { key: 'warehouse', label: 'Gudang', render: r => r.warehouse?.name || '-' },
        { key: 'is_active', label: 'Status', render: r => r.is_active ? <Badge color="success">Aktif</Badge> : <Badge color="danger">Nonaktif</Badge> },
        { key: 'actions', label: 'Aksi', render: r => (
            <div className="flex items-center gap-3">
                <Link href={`/users/${r.id}/edit`} className="text-surface-500 hover:text-primary-600"><Pencil size={16}/></Link>
                <DeleteButton href={`/users/${r.id}`} name={r.name} />
            </div>
        )},
    ];
    return (
        <AuthenticatedLayout title="Users"><Head title="Users" />
            <PageHeader title="Users" subtitle={`${users.total} user`} actions={<LinkButton href="/users/create"><Plus size={16} className="inline mr-1 -mt-0.5"/>Tambah</LinkButton>} />
            <DataTable columns={columns} data={users.data} pagination={users} filters={filters} />
        </AuthenticatedLayout>
    );
}
