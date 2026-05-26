import { useEffect, useRef } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';
import { PageHeader } from '@/Components/UI';
import L from 'leaflet';
import { MapContainer, TileLayer, Marker, Popup } from 'react-leaflet';

// Fix leaflet default icon
import markerIcon2x from 'leaflet/dist/images/marker-icon-2x.png';
import markerIcon from 'leaflet/dist/images/marker-icon.png';
import markerShadow from 'leaflet/dist/images/marker-shadow.png';

delete L.Icon.Default.prototype._getIconUrl;
L.Icon.Default.mergeOptions({
    iconUrl: markerIcon,
    iconRetinaUrl: markerIcon2x,
    shadowUrl: markerShadow,
});

export default function WarehouseMap({ warehouses }) {
    const center = [-2.5, 118]; // Center of Indonesia

    return (
        <AuthenticatedLayout title="Peta Gudang">
            <Head title="Peta Gudang" />
            <PageHeader title="Peta Gudang" subtitle="Lokasi 5 gudang PT Maju Bersama Digital" />
            <div className="bg-white rounded-xl border border-surface-200 overflow-hidden" style={{ height: '600px' }}>
                <MapContainer center={center} zoom={5} style={{ height: '100%', width: '100%' }} scrollWheelZoom={true}>
                    <TileLayer
                        attribution='&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
                        url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png"
                    />
                    {warehouses.map(w => (
                        <Marker key={w.id} position={[w.latitude, w.longitude]}>
                            <Popup>
                                <div className="text-sm">
                                    <p className="font-bold text-base">{w.name}</p>
                                    <p className="text-gray-500">{w.city}</p>
                                    <hr className="my-1" />
                                    <p>Total Stok: <strong>{w.total_stock.toLocaleString('id-ID')}</strong> unit</p>
                                </div>
                            </Popup>
                        </Marker>
                    ))}
                </MapContainer>
            </div>
        </AuthenticatedLayout>
    );
}
