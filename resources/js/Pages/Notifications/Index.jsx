import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, router } from '@inertiajs/react';
import { PageHeader, Badge } from '@/Components/UI';
import { Bell, CheckCheck } from 'lucide-react';

const severityColors = { info: 'info', warning: 'warning', critical: 'danger' };

export default function Index({ notifications }) {
    return (
        <AuthenticatedLayout title="Notifikasi"><Head title="Notifikasi" />
            <PageHeader title="Notifikasi" actions={
                <button onClick={() => router.patch('/notifications/mark-all-read')} className="px-4 py-2 text-sm bg-surface-100 hover:bg-surface-200 rounded-xl flex items-center gap-2"><CheckCheck size={16}/>Tandai semua dibaca</button>
            } />
            <div className="space-y-2">
                {notifications.data.length === 0 ? (
                    <div className="bg-white rounded-xl border border-surface-200 p-12 text-center text-surface-400">
                        <Bell size={48} className="mx-auto mb-4 opacity-30" /><p>Tidak ada notifikasi</p>
                    </div>
                ) : notifications.data.map(n => (
                    <div key={n.id} className={`bg-white rounded-xl border p-4 flex items-start gap-4 transition hover:shadow-sm ${n.is_read ? 'border-surface-200' : 'border-primary-200 bg-primary-50/30'}`}>
                        <div className="flex-1">
                            <div className="flex items-center gap-2 mb-1">
                                <Badge color={severityColors[n.severity]}>{n.severity}</Badge>
                                <span className="font-semibold text-sm text-surface-900">{n.title}</span>
                                {!n.is_read && <span className="w-2 h-2 bg-primary-500 rounded-full" />}
                            </div>
                            <p className="text-sm text-surface-600">{n.message}</p>
                            <p className="text-xs text-surface-400 mt-1">{new Date(n.created_at).toLocaleString('id-ID')}</p>
                        </div>
                        {!n.is_read && <button onClick={() => router.patch(`/notifications/${n.id}/read`)} className="text-xs text-primary-600 hover:underline whitespace-nowrap">Tandai dibaca</button>}
                    </div>
                ))}
            </div>
        </AuthenticatedLayout>
    );
}
