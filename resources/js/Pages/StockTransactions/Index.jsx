import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';
import { DataTable, PageHeader, Badge, LinkButton } from '@/Components/UI';
import { ArrowDownToLine, ArrowUpFromLine } from 'lucide-react';

export default function Index({ transactions, warehouses, filters }) {
    const columns = [
        { key: 'transaction_code', label: 'Kode', render: r => <span className="font-mono text-xs">{r.transaction_code}</span> },
        { key: 'type', label: 'Tipe', render: r => r.type==='in' ? <Badge color="success">Masuk</Badge> : <Badge color="danger">Keluar</Badge> },
        { key: 'product', label: 'Produk', render: r => <div><p className="font-medium">{r.product?.name}</p><p className="text-xs text-surface-500">{r.product?.sku}</p></div> },
        { key: 'warehouse', label: 'Gudang', render: r => r.warehouse?.name },
        { key: 'quantity', label: 'Qty', sortable: true, render: r => <span className="font-bold">{r.quantity}</span> },
        { key: 'transaction_date', label: 'Tanggal', sortable: true, render: r => new Date(r.transaction_date).toLocaleDateString('id-ID') },
        { key: 'creator', label: 'Dibuat', render: r => r.creator?.name },
    ];
    return (
        <AuthenticatedLayout title="Transaksi Stok"><Head title="Transaksi Stok" />
            <PageHeader title="Transaksi Stok" subtitle={`${transactions.total} transaksi`}
                actions={<div className="flex gap-2">
                    <LinkButton href="/stock-transactions/create-in"><ArrowDownToLine size={16} className="inline mr-1 -mt-0.5"/>Barang Masuk</LinkButton>
                    <LinkButton href="/stock-transactions/create-out" color="secondary"><ArrowUpFromLine size={16} className="inline mr-1 -mt-0.5"/>Barang Keluar</LinkButton>
                </div>} />
            <DataTable columns={columns} data={transactions.data} pagination={transactions} filters={filters} searchPlaceholder="Cari kode atau produk..." />
        </AuthenticatedLayout>
    );
}
