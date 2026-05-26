import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, router } from '@inertiajs/react';
import { PageHeader, Badge } from '@/Components/UI';

const severityColors = { critical: 'danger', warning: 'warning', info: 'info' };

export default function Index({ logs, filters }) {
    return (
        <AuthenticatedLayout title="Error Log"><Head title="Error Log" />
            <PageHeader title="Error Log" subtitle={`${logs.total} error`} />
            <div className="space-y-2">
                {logs.data.length === 0 ? (
                    <div className="bg-white rounded-xl border border-surface-200 p-12 text-center text-surface-400">Tidak ada error log</div>
                ) : logs.data.map(log => (
                    <div key={log.id} className={`bg-white rounded-xl border p-4 ${log.resolved_at ? 'border-surface-200 opacity-60' : 'border-red-200'}`}>
                        <div className="flex items-center justify-between mb-2">
                            <div className="flex items-center gap-2">
                                <Badge color={severityColors[log.severity]}>{log.severity}</Badge>
                                <span className="text-xs text-surface-400">{new Date(log.created_at).toLocaleString('id-ID')}</span>
                                {log.resolved_at && <Badge color="success">Resolved</Badge>}
                            </div>
                            {!log.resolved_at && <button onClick={() => router.patch(`/error-logs/${log.id}/resolve`)} className="text-xs text-green-600 hover:underline">Mark Resolved</button>}
                        </div>
                        <p className="text-sm text-surface-900 font-medium">{log.message}</p>
                        {log.file && <p className="text-xs text-surface-500 mt-1">{log.file}:{log.line}</p>}
                    </div>
                ))}
            </div>
        </AuthenticatedLayout>
    );
}
