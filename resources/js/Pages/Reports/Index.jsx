import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';
import { PageHeader } from '@/Components/UI';
import { FileText, Download } from 'lucide-react';

const reports = [
    { title: 'Laporan Stok Inventory', desc: 'Stok seluruh produk per gudang', pdfUrl: '/export/inventory', csvUrl: '/export/inventory?format=csv' },
    { title: 'Laporan Stok Rendah', desc: 'Produk di bawah stok minimum', pdfUrl: '/export/low-stock' },
    { title: 'Laporan Transaksi Stok', desc: 'Riwayat barang masuk dan keluar', pdfUrl: '/export/transactions', csvUrl: '/export/transactions?format=csv' },
    { title: 'Laporan Transfer Gudang', desc: 'Riwayat transfer antar gudang', pdfUrl: '/export/transfers' },
];

export default function Index() {
    return (
        <AuthenticatedLayout title="Laporan"><Head title="Laporan" />
            <PageHeader title="Laporan" subtitle="Unduh laporan dalam format PDF atau CSV" />
            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                {reports.map((r, i) => (
                    <div key={i} className="bg-white rounded-xl border border-surface-200 p-5 hover:shadow-md transition">
                        <div className="flex items-start gap-4">
                            <div className="w-10 h-10 bg-primary-50 text-primary-600 rounded-xl flex items-center justify-center"><FileText size={20}/></div>
                            <div className="flex-1">
                                <h3 className="font-semibold text-surface-900">{r.title}</h3>
                                <p className="text-sm text-surface-500 mt-1">{r.desc}</p>
                                <div className="flex gap-2 mt-3">
                                    <a href={r.pdfUrl} className="px-3 py-1.5 bg-red-50 text-red-700 text-xs font-medium rounded-lg hover:bg-red-100 flex items-center gap-1"><Download size={12}/>PDF</a>
                                    {r.csvUrl && <a href={r.csvUrl} className="px-3 py-1.5 bg-green-50 text-green-700 text-xs font-medium rounded-lg hover:bg-green-100 flex items-center gap-1"><Download size={12}/>CSV</a>}
                                </div>
                            </div>
                        </div>
                    </div>
                ))}
            </div>
        </AuthenticatedLayout>
    );
}
