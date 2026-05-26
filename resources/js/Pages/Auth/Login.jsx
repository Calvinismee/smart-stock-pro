import { useForm, Head } from '@inertiajs/react';

export default function Login() {
    const { data, setData, post, processing, errors } = useForm({ email: '', password: '', remember: false });

    const submit = (e) => { e.preventDefault(); post('/login'); };

    return (
        <>
            <Head title="Login" />
            <div className="min-h-screen bg-gradient-to-br from-surface-900 via-surface-950 to-primary-950 flex items-center justify-center p-4">
                <div className="w-full max-w-md">
                    <div className="text-center mb-8">
                        <div className="inline-flex items-center justify-center w-16 h-16 bg-primary-500 rounded-2xl mb-4 shadow-lg shadow-primary-500/30">
                            <span className="text-2xl font-bold text-white">SS</span>
                        </div>
                        <h1 className="text-3xl font-bold text-white">SmartStock Pro</h1>
                        <p className="text-surface-400 mt-2">Inventory Management System</p>
                    </div>

                    <form onSubmit={submit} className="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl p-8 shadow-2xl">
                        <h2 className="text-xl font-semibold text-white mb-6">Masuk ke Akun Anda</h2>

                        <div className="space-y-4">
                            <div>
                                <label className="block text-sm font-medium text-surface-300 mb-1.5">Email</label>
                                <input type="email" value={data.email} onChange={e => setData('email', e.target.value)}
                                    className="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-surface-500 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition"
                                    placeholder="email@example.com" autoFocus />
                                {errors.email && <p className="mt-1 text-sm text-red-400">{errors.email}</p>}
                            </div>

                            <div>
                                <label className="block text-sm font-medium text-surface-300 mb-1.5">Password</label>
                                <input type="password" value={data.password} onChange={e => setData('password', e.target.value)}
                                    className="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-surface-500 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition"
                                    placeholder="••••••••" />
                                {errors.password && <p className="mt-1 text-sm text-red-400">{errors.password}</p>}
                            </div>

                            <label className="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" checked={data.remember} onChange={e => setData('remember', e.target.checked)}
                                    className="w-4 h-4 rounded border-white/30 bg-white/10 text-primary-500 focus:ring-primary-500" />
                                <span className="text-sm text-surface-300">Ingat saya</span>
                            </label>
                        </div>

                        <button type="submit" disabled={processing}
                            className="w-full mt-6 px-4 py-3 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-xl transition-all duration-200 disabled:opacity-50 shadow-lg shadow-primary-600/30 hover:shadow-primary-600/50">
                            {processing ? 'Memproses...' : 'Masuk'}
                        </button>
                    </form>

                    <p className="text-center text-surface-500 text-xs mt-6">PT Maju Bersama Digital © 2024</p>
                </div>
            </div>
        </>
    );
}
