import { useState } from 'react';
import { Link, usePage, router } from '@inertiajs/react';
import { Menu, X, LayoutDashboard, Package, Tags, Building2, Truck, Users, ArrowDownToLine, ArrowUpFromLine, ArrowLeftRight, Bell, FileText, Upload, ClipboardList, AlertTriangle, Map, LogOut, ChevronDown, Box, Activity } from 'lucide-react';

const navigation = {
    admin: [
        { name: 'Dashboard', href: '/', icon: LayoutDashboard },
        { name: 'Produk & Stok', href: '/products', icon: Package },
        { name: 'Kategori', href: '/categories', icon: Tags },
        { name: 'Gudang', href: '/warehouses', icon: Building2 },
        { name: 'Supplier', href: '/suppliers', icon: Truck },
        { name: 'Users', href: '/users', icon: Users },
        { name: 'Transaksi Stok', href: '/stock-transactions', icon: ClipboardList },
        { name: 'Transfer Gudang', href: '/stock-transfers', icon: ArrowLeftRight },
        { name: 'Notifikasi', href: '/notifications', icon: Bell },
        { name: 'Laporan', href: '/reports', icon: FileText },
        { name: 'Import', href: '/import', icon: Upload },
        { name: 'Audit Log', href: '/audit-logs', icon: ClipboardList },
        { name: 'Error Log', href: '/error-logs', icon: AlertTriangle },
        { name: 'Peta Gudang', href: '/warehouse-map', icon: Map },
        { name: 'Server Monitor', href: '/pulse', icon: Activity, external: true },
    ],
    manager: [
        { name: 'Dashboard', href: '/', icon: LayoutDashboard },
        { name: 'Produk & Stok', href: '/products', icon: Package },
        { name: 'Kategori', href: '/categories', icon: Tags },
        { name: 'Gudang', href: '/warehouses', icon: Building2 },
        { name: 'Supplier', href: '/suppliers', icon: Truck },
        { name: 'Transaksi Stok', href: '/stock-transactions', icon: ClipboardList },
        { name: 'Transfer Gudang', href: '/stock-transfers', icon: ArrowLeftRight },
        { name: 'Notifikasi', href: '/notifications', icon: Bell },
        { name: 'Laporan', href: '/reports', icon: FileText },
        { name: 'Import', href: '/import', icon: Upload },
        { name: 'Peta Gudang', href: '/warehouse-map', icon: Map },
    ],
    staff: [
        { name: 'My Warehouse', href: '/my-warehouse', icon: Building2 },
        { name: 'Monitoring Stok', href: '/inventory-stocks', icon: Box },
        { name: 'Transaksi Stok', href: '/stock-transactions', icon: ClipboardList },
        { name: 'Transfer Gudang', href: '/stock-transfers', icon: ArrowLeftRight },
        { name: 'Notifikasi', href: '/notifications', icon: Bell },
    ],
    auditor: [
        { name: 'Dashboard', href: '/', icon: LayoutDashboard },
        { name: 'Produk & Stok', href: '/products', icon: Package },
        { name: 'Monitoring Stok', href: '/inventory-stocks', icon: Box },
        { name: 'Laporan', href: '/reports', icon: FileText },
        { name: 'Peta Gudang', href: '/warehouse-map', icon: Map },
    ],
};

const roleLabels = { admin: 'Administrator', manager: 'Manajer Gudang', staff: 'Staf Gudang', auditor: 'Auditor' };

export default function AuthenticatedLayout({ children, title }) {
    const { auth, flash, notifications_count } = usePage().props;
    const [sidebarOpen, setSidebarOpen] = useState(false);
    const [userMenuOpen, setUserMenuOpen] = useState(false);
    const user = auth.user;
    const navItems = navigation[user.role] || navigation.auditor;
    const currentPath = window.location.pathname;

    return (
        <div className="min-h-screen bg-surface-50 flex">
            {/* Mobile overlay */}
            {sidebarOpen && <div className="fixed inset-0 bg-black/50 z-40 lg:hidden" onClick={() => setSidebarOpen(false)} />}

            {/* Sidebar */}
            <aside className={`fixed inset-y-0 left-0 z-50 w-64 bg-surface-950 text-white transform transition-transform duration-200 ease-in-out lg:translate-x-0 lg:static lg:inset-auto ${sidebarOpen ? 'translate-x-0' : '-translate-x-full'}`}>
                <div className="flex items-center justify-between h-16 px-4 border-b border-surface-800">
                    <Link href="/" className="flex items-center gap-2">
                        <div className="w-8 h-8 bg-primary-500 rounded-lg flex items-center justify-center font-bold text-sm">SS</div>
                        <span className="text-lg font-semibold">SmartStock</span>
                    </Link>
                    <button onClick={() => setSidebarOpen(false)} className="lg:hidden text-surface-400 hover:text-white"><X size={20} /></button>
                </div>
                <nav className="mt-4 px-3 space-y-1 overflow-y-auto" style={{ maxHeight: 'calc(100vh - 4rem)' }}>
                    {navItems.map((item) => {
                        const isActive = currentPath === item.href || (item.href !== '/' && currentPath.startsWith(item.href));
                        const Component = item.external ? 'a' : Link;
                        return (
                            <Component key={item.href} href={item.href}
                                className={`flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors ${isActive ? 'bg-primary-600 text-white' : 'text-surface-300 hover:bg-surface-800 hover:text-white'}`}>
                                <item.icon size={18} />
                                <span>{item.name}</span>
                                {item.name === 'Notifikasi' && notifications_count > 0 && (
                                    <span className="ml-auto bg-red-500 text-white text-xs rounded-full px-2 py-0.5">{notifications_count}</span>
                                )}
                            </Component>
                        );
                    })}
                </nav>
            </aside>

            {/* Main content */}
            <div className="flex-1 flex flex-col min-w-0">
                {/* Topbar */}
                <header className="h-16 bg-white border-b border-surface-200 flex items-center justify-between px-4 lg:px-6 sticky top-0 z-30">
                    <div className="flex items-center gap-4">
                        <button onClick={() => setSidebarOpen(true)} className="lg:hidden text-surface-600 hover:text-surface-900"><Menu size={24} /></button>
                        {title && <h1 className="text-lg font-semibold text-surface-900 hidden sm:block">{title}</h1>}
                    </div>
                    <div className="flex items-center gap-4">
                        <Link href="/notifications" className="relative text-surface-500 hover:text-surface-700">
                            <Bell size={20} />
                            {notifications_count > 0 && <span className="absolute -top-1 -right-1 w-4 h-4 bg-red-500 text-white text-[10px] rounded-full flex items-center justify-center">{notifications_count}</span>}
                        </Link>
                        <div className="relative">
                            <button onClick={() => setUserMenuOpen(!userMenuOpen)} className="flex items-center gap-2 text-sm">
                                <div className="w-8 h-8 bg-primary-100 text-primary-700 rounded-full flex items-center justify-center font-semibold text-xs">{user.name.charAt(0)}</div>
                                <div className="hidden sm:block text-left">
                                    <div className="font-medium text-surface-900 text-sm">{user.name}</div>
                                    <div className="text-xs text-surface-500">{roleLabels[user.role]}</div>
                                </div>
                                <ChevronDown size={16} className="text-surface-400" />
                            </button>
                            {userMenuOpen && (
                                <>
                                    <div className="fixed inset-0 z-40" onClick={() => setUserMenuOpen(false)} />
                                    <div className="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-surface-200 py-1 z-50">
                                        <button onClick={() => { router.post('/logout'); setUserMenuOpen(false); }}
                                            className="flex items-center gap-2 w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                            <LogOut size={16} /><span>Logout</span>
                                        </button>
                                    </div>
                                </>
                            )}
                        </div>
                    </div>
                </header>

                {/* Flash messages */}
                {flash?.success && (
                    <div className="mx-4 lg:mx-6 mt-4 px-4 py-3 bg-green-50 border border-green-200 text-green-800 rounded-lg text-sm">{flash.success}</div>
                )}
                {flash?.error && (
                    <div className="mx-4 lg:mx-6 mt-4 px-4 py-3 bg-red-50 border border-red-200 text-red-800 rounded-lg text-sm">{flash.error}</div>
                )}

                {/* Page content */}
                <main className="flex-1 p-4 lg:p-6">{children}</main>
            </div>
        </div>
    );
}
