import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link } from '@inertiajs/react';
import { DataTable, PageHeader, Badge, DeleteButton, LinkButton } from '@/Components/UI';
import { Plus, Eye, Pencil } from 'lucide-react';

export default function Index({ products, categories, filters }) {
    const columns = [
        { key: 'sku', label: 'SKU', sortable: true, render: r => <span className="font-mono text-xs">{r.sku}</span> },
        { key: 'name', label: 'Nama Produk', sortable: true, render: r => (
            <div className="flex items-center gap-3">
                {r.image ? <img src={`/storage/${r.image}`} className="w-10 h-10 rounded-lg object-cover" /> : <div className="w-10 h-10 rounded-lg bg-surface-100 flex items-center justify-center text-surface-400 text-xs">No img</div>}
                <div><p className="font-medium text-surface-900">{r.name}</p><p className="text-xs text-surface-500">{r.category?.name}</p></div>
            </div>
        )},
        { key: 'selling_price', label: 'Harga Jual', sortable: true, render: r => 'Rp ' + Number(r.selling_price).toLocaleString('id-ID') },
        { key: 'inventory_stocks_sum_quantity', label: 'Stok', sortable: false, render: r => {
            const qty = r.inventory_stocks_sum_quantity || 0;
            return <span className={qty <= r.minimum_stock ? 'text-red-600 font-bold' : ''}>{qty}</span>;
        }},
        { key: 'is_active', label: 'Status', render: r => r.is_active ? <Badge color="success">Aktif</Badge> : <Badge color="danger">Nonaktif</Badge> },
        { key: 'actions', label: 'Aksi', render: r => (
            <div className="flex items-center gap-3">
                <Link href={`/products/${r.id}`} className="text-surface-500 hover:text-primary-600"><Eye size={16}/></Link>
                <Link href={`/products/${r.id}/edit`} className="text-surface-500 hover:text-primary-600"><Pencil size={16}/></Link>
                <DeleteButton href={`/products/${r.id}`} name={r.name} />
            </div>
        )},
    ];

    return (
        <AuthenticatedLayout title="Produk">
            <Head title="Produk" />
            <PageHeader title="Produk" subtitle={`${products.total} produk terdaftar`} actions={<LinkButton href="/products/create"><Plus size={16} className="inline mr-1 -mt-0.5"/>Tambah Produk</LinkButton>} />
            <DataTable columns={columns} data={products.data} pagination={products} filters={filters} searchPlaceholder="Cari SKU atau nama..." />
        </AuthenticatedLayout>
    );
}
