import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, useForm, usePage } from '@inertiajs/react';
import { PageHeader } from '@/Components/UI';
import { Upload, Download, FileSpreadsheet } from 'lucide-react';

function ImportCard({ title, desc, action, templateUrl, templateName }) {
    const { data, setData, post, processing, errors } = useForm({ file: null });
    const { flash } = usePage().props;

    return (
        <div className="bg-white rounded-xl border border-surface-200 p-6">
            <h3 className="font-semibold text-surface-900 text-lg mb-1">{title}</h3>
            <p className="text-sm text-surface-500 mb-4">{desc}</p>
            <a href={templateUrl} className="inline-flex items-center gap-2 text-sm text-primary-600 hover:underline mb-4"><Download size={14}/>{templateName}</a>
            <form onSubmit={e => { e.preventDefault(); post(action, { forceFormData: true }); }}>
                <div className="flex items-center gap-3">
                    <label className="flex-1 border-2 border-dashed border-surface-200 rounded-xl p-4 text-center cursor-pointer hover:border-primary-400 transition">
                        <FileSpreadsheet size={24} className="mx-auto text-surface-400 mb-2" />
                        <span className="text-sm text-surface-600">{data.file ? data.file.name : 'Pilih file CSV/Excel'}</span>
                        <input type="file" accept=".csv,.xlsx,.xls" className="hidden" onChange={e => setData('file', e.target.files[0])} />
                    </label>
                    <button type="submit" disabled={processing || !data.file}
                        className="px-5 py-3 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-xl transition disabled:opacity-50 flex items-center gap-2">
                        <Upload size={16}/>{processing ? 'Importing...' : 'Import'}
                    </button>
                </div>
                {errors.file && <p className="text-sm text-red-500 mt-2">{errors.file}</p>}
            </form>
        </div>
    );
}

export default function Index() {
    return (
        <AuthenticatedLayout title="Import Data"><Head title="Import Data" />
            <PageHeader title="Import Data" subtitle="Import produk atau stok awal dari file CSV/Excel" />
            <div className="space-y-6 max-w-2xl">
                <ImportCard title="Import Produk" desc="Import data produk baru ke sistem" action="/import/products" templateUrl="/import/template/products" templateName="Download template produk" />
                <ImportCard title="Import Stok Awal" desc="Import jumlah stok per produk per gudang" action="/import/stock" templateUrl="/import/template/stock" templateName="Download template stok" />
            </div>
        </AuthenticatedLayout>
    );
}
