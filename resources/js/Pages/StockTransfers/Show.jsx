import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';
import { PageHeader, Badge } from '@/Components/UI';

export default function Show({ transfer }) {
    return (
        <AuthenticatedLayout title={transfer.transfer_code}><Head title={transfer.transfer_code} />
            <PageHeader title="Detail Transfer" subtitle={transfer.transfer_code} />
            <div className="bg-white rounded-xl border border-surface-200 p-6 max-w-2xl">
                <div className="grid grid-cols-2 gap-4 text-sm">
                    <div><span className="text-surface-500">Kode Transfer</span><p className="font-mono font-medium">{transfer.transfer_code}</p></div>
                    <div><span className="text-surface-500">Status</span><p><Badge color={transfer.status==='completed'?'success':'warning'}>{transfer.status}</Badge></p></div>
                    <div><span className="text-surface-500">Produk</span><p className="font-medium">{transfer.product?.name}</p></div>
                    <div><span className="text-surface-500">Jumlah</span><p className="font-bold text-lg">{transfer.quantity}</p></div>
                    <div><span className="text-surface-500">Gudang Asal</span><p className="font-medium">{transfer.source_warehouse?.name}</p></div>
                    <div><span className="text-surface-500">Gudang Tujuan</span><p className="font-medium">{transfer.destination_warehouse?.name}</p></div>
                    <div><span className="text-surface-500">Tanggal</span><p>{new Date(transfer.transfer_date).toLocaleDateString('id-ID')}</p></div>
                    <div><span className="text-surface-500">Dibuat oleh</span><p>{transfer.creator?.name}</p></div>
                </div>
                {transfer.notes && <div className="mt-4 pt-4 border-t border-surface-100"><span className="text-surface-500 text-sm">Catatan</span><p className="mt-1">{transfer.notes}</p></div>}
            </div>
        </AuthenticatedLayout>
    );
}
