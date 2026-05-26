import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';
import { DataTable, PageHeader } from '@/Components/UI';

export default function Index({ logs, filters, modules }) {
    const columns = [
        { key: 'created_at', label: 'Waktu', render: r => <span className="text-xs">{new Date(r.created_at).toLocaleString('id-ID')}</span> },
        { key: 'user', label: 'User', render: r => r.user?.name || 'System' },
        { key: 'action', label: 'Aksi', render: r => <span className="font-mono text-xs bg-surface-100 px-2 py-0.5 rounded">{r.action}</span> },
        { key: 'module', label: 'Modul', render: r => <span className="capitalize">{r.module}</span> },
        { key: 'description', label: 'Deskripsi', render: r => <span className="text-sm text-surface-600">{r.description}</span> },
        { key: 'ip_address', label: 'IP', render: r => <span className="text-xs text-surface-400">{r.ip_address}</span> },
    ];
    return (
        <AuthenticatedLayout title="Audit Log"><Head title="Audit Log" />
            <PageHeader title="Audit Log" subtitle={`${logs.total} log tercatat`} />
            <DataTable columns={columns} data={logs.data} pagination={logs} filters={filters} searchPlaceholder="Cari deskripsi..." />
        </AuthenticatedLayout>
    );
}
