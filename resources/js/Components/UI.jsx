import { Link, router } from '@inertiajs/react';
import { ChevronLeft, ChevronRight, Search, ArrowUpDown } from 'lucide-react';
import { useState, useCallback } from 'react';

export function DataTable({ columns, data, pagination, filters = {}, searchPlaceholder = 'Cari...' }) {
    const [search, setSearch] = useState(filters.search || '');

    const handleSearch = useCallback((val) => {
        setSearch(val);
        const timeout = setTimeout(() => {
            router.get(window.location.pathname, { ...filters, search: val, page: 1 }, { preserveState: true, replace: true });
        }, 400);
        return () => clearTimeout(timeout);
    }, [filters]);

    const handleSort = (field) => {
        const dir = filters.sort === field && filters.direction === 'asc' ? 'desc' : 'asc';
        router.get(window.location.pathname, { ...filters, sort: field, direction: dir, page: 1 }, { preserveState: true, replace: true });
    };

    return (
        <div>
            <div className="mb-4">
                <div className="relative">
                    <Search size={16} className="absolute left-3 top-1/2 -translate-y-1/2 text-surface-400" />
                    <input type="text" value={search} onChange={e => handleSearch(e.target.value)}
                        className="w-full sm:w-72 pl-10 pr-4 py-2.5 border border-surface-200 rounded-xl bg-white text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                        placeholder={searchPlaceholder} />
                </div>
            </div>
            <div className="overflow-x-auto bg-white rounded-xl border border-surface-200">
                <table className="w-full text-sm">
                    <thead>
                        <tr className="border-b border-surface-200 bg-surface-50">
                            {columns.map(col => (
                                <th key={col.key} className="px-4 py-3 text-left text-xs font-semibold text-surface-600 uppercase tracking-wider">
                                    {col.sortable ? (
                                        <button onClick={() => handleSort(col.key)} className="flex items-center gap-1 hover:text-surface-900">
                                            {col.label} <ArrowUpDown size={12} />
                                        </button>
                                    ) : col.label}
                                </th>
                            ))}
                        </tr>
                    </thead>
                    <tbody className="divide-y divide-surface-100">
                        {data.length === 0 ? (
                            <tr><td colSpan={columns.length} className="px-4 py-12 text-center text-surface-400">Tidak ada data ditemukan</td></tr>
                        ) : data.map((row, i) => (
                            <tr key={row.id || i} className="hover:bg-surface-50 transition-colors">
                                {columns.map(col => (
                                    <td key={col.key} className="px-4 py-3 text-surface-700">{col.render ? col.render(row) : row[col.key]}</td>
                                ))}
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>
            {pagination && pagination.last_page > 1 && (
                <div className="flex items-center justify-between mt-4 text-sm text-surface-600">
                    <span>Menampilkan {pagination.from}-{pagination.to} dari {pagination.total}</span>
                    <div className="flex gap-1">
                        {pagination.links?.map((link, i) => (
                            <Link key={i} href={link.url || '#'}
                                className={`px-3 py-1.5 rounded-lg border text-sm transition ${link.active ? 'bg-primary-600 text-white border-primary-600' : link.url ? 'border-surface-200 hover:bg-surface-100' : 'border-surface-100 text-surface-300 cursor-not-allowed'}`}
                                dangerouslySetInnerHTML={{ __html: link.label }} preserveState />
                        ))}
                    </div>
                </div>
            )}
        </div>
    );
}

export function PageHeader({ title, subtitle, actions }) {
    return (
        <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h1 className="text-2xl font-bold text-surface-900">{title}</h1>
                {subtitle && <p className="text-surface-500 text-sm mt-1">{subtitle}</p>}
            </div>
            {actions && <div className="flex gap-2">{actions}</div>}
        </div>
    );
}

export function FormCard({ children, title, onSubmit, processing, submitLabel = 'Simpan' }) {
    return (
        <form onSubmit={onSubmit} className="bg-white rounded-xl border border-surface-200 p-6 max-w-2xl">
            {title && <h2 className="text-lg font-semibold text-surface-900 mb-6">{title}</h2>}
            <div className="space-y-4">{children}</div>
            <div className="flex gap-3 mt-6 pt-4 border-t border-surface-200">
                <button type="submit" disabled={processing}
                    className="px-6 py-2.5 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-xl transition disabled:opacity-50">
                    {processing ? 'Menyimpan...' : submitLabel}
                </button>
                <button type="button" onClick={() => window.history.back()}
                    className="px-6 py-2.5 border border-surface-200 text-surface-700 hover:bg-surface-50 font-medium rounded-xl transition">
                    Batal
                </button>
            </div>
        </form>
    );
}

export function FormField({ label, error, children }) {
    return (
        <div>
            <label className="block text-sm font-medium text-surface-700 mb-1.5">{label}</label>
            {children}
            {error && <p className="mt-1 text-sm text-red-500">{error}</p>}
        </div>
    );
}

export function Input({ error, ...props }) {
    return (
        <input {...props} className={`w-full px-4 py-2.5 border rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition ${error ? 'border-red-300 bg-red-50' : 'border-surface-200 bg-white'} ${props.className || ''}`} />
    );
}

export function Select({ options = [], error, placeholder, ...props }) {
    return (
        <select {...props} className={`w-full px-4 py-2.5 border rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition ${error ? 'border-red-300 bg-red-50' : 'border-surface-200 bg-white'}`}>
            {placeholder && <option value="">{placeholder}</option>}
            {options.map(o => <option key={o.value} value={o.value}>{o.label}</option>)}
        </select>
    );
}

export function Textarea({ error, ...props }) {
    return <textarea {...props} className={`w-full px-4 py-2.5 border rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition ${error ? 'border-red-300 bg-red-50' : 'border-surface-200 bg-white'}`} rows={3} />;
}

export function DeleteButton({ href, name }) {
    const [confirming, setConfirming] = useState(false);
    if (confirming) {
        return (
            <div className="flex items-center gap-2">
                <span className="text-xs text-red-600">Hapus {name}?</span>
                <button onClick={() => router.delete(href)} className="text-xs px-2 py-1 bg-red-600 text-white rounded-lg">Ya</button>
                <button onClick={() => setConfirming(false)} className="text-xs px-2 py-1 bg-surface-200 rounded-lg">Batal</button>
            </div>
        );
    }
    return <button onClick={() => setConfirming(true)} className="text-red-500 hover:text-red-700 text-sm">Hapus</button>;
}

export function Badge({ children, color = 'primary' }) {
    const colors = {
        primary: 'bg-primary-100 text-primary-700', success: 'bg-green-100 text-green-700',
        danger: 'bg-red-100 text-red-700', warning: 'bg-amber-100 text-amber-700', info: 'bg-blue-100 text-blue-700',
    };
    return <span className={`px-2.5 py-0.5 text-xs font-medium rounded-full ${colors[color]}`}>{children}</span>;
}

export function LinkButton({ href, children, color = 'primary' }) {
    const colors = {
        primary: 'bg-primary-600 hover:bg-primary-700 text-white', secondary: 'bg-surface-100 hover:bg-surface-200 text-surface-700',
    };
    return <Link href={href} className={`px-4 py-2.5 font-medium rounded-xl text-sm transition ${colors[color]}`}>{children}</Link>;
}
