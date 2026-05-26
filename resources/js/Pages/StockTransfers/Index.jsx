import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link } from '@inertiajs/react';
import { DataTable, PageHeader, Badge, LinkButton } from '@/Components/UI';
import { Plus, Eye } from 'lucide-react';

export default function Index({ transfers, filters }) {
    const columns = [
        { key: 'transfer_code', label: 'Kode', render: r => <span className="font-mono text-xs">{r.transfer_code}</span> },
        { key: 'product', label: 'Produk', render: r => <div><p className="font-medium">{r.product?.name}</p><p className="text-xs text-surface-500">{r.product?.sku}</p></div> },
        { key: 'route', label: 'Rute', render: r => <span className="text-sm">{r.source_warehouse?.name} → {r.destination_warehouse?.name}</span> },
        { key: 'quantity', label: 'Qty', render: r => <span className="font-bold">{r.quantity}</span> },
        { key: 'transfer_date', label: 'Tanggal', sortable: true, render: r => new Date(r.transfer_date).toLocaleDateString('id-ID') },
        { key: 'status', label: 'Status', render: r => <Badge color={r.status==='completed'?'success':r.status==='pending'?'warning':'danger'}>{r.status}</Badge> },
        { key: 'actions', label: '', render: r => <Link href={`/stock-transfers/${r.id}`} className="text-surface-500 hover:text-primary-600"><Eye size={16}/></Link> },
    ];
    return (
        <AuthenticatedLayout title="Transfer Gudang"><Head title="Transfer Gudang" />
            <PageHeader title="Transfer Gudang" subtitle={`${transfers.total} transfer`} actions={<LinkButton href="/stock-transfers/create"><Plus size={16} className="inline mr-1 -mt-0.5"/>Buat Transfer</LinkButton>} />
            <DataTable columns={columns} data={transfers.data} pagination={transfers} filters={filters} searchPlaceholder="Cari kode transfer..." />
        </AuthenticatedLayout>
    );
}
