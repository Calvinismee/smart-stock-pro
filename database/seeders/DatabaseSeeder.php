<?php

namespace Database\Seeders;

use App\Models\AuditLog;
use App\Models\Category;
use App\Models\InventoryStock;
use App\Models\Notification;
use App\Models\Product;
use App\Models\StockTransaction;
use App\Models\StockTransfer;
use App\Models\Supplier;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create warehouses (must be first, users reference them)
        $warehouses = $this->seedWarehouses();

        // 2. Create users
        $users = $this->seedUsers($warehouses);

        // 3. Create categories
        $categories = $this->seedCategories();

        // 4. Create suppliers
        $suppliers = $this->seedSuppliers();

        // 5. Create products
        $products = $this->seedProducts($categories, $suppliers);

        // 6. Create inventory stocks
        $this->seedInventoryStocks($products, $warehouses);

        // 7. Create sample transactions
        $this->seedStockTransactions($products, $warehouses, $suppliers, $users);

        // 8. Create sample transfers
        $this->seedStockTransfers($products, $warehouses, $users);

        // 9. Create sample notifications
        $this->seedNotifications($users);

        // 10. Create sample audit logs
        $this->seedAuditLogs($users);
    }

    private function seedWarehouses(): array
    {
        $data = [
            ['code' => 'WH-JKT', 'name' => 'Gudang Jakarta', 'city' => 'Jakarta', 'address' => 'Jl. Industri No. 1, Cakung, Jakarta Timur', 'latitude' => -6.1751, 'longitude' => 106.8650, 'phone' => '021-5551234'],
            ['code' => 'WH-SBY', 'name' => 'Gudang Surabaya', 'city' => 'Surabaya', 'address' => 'Jl. Rungkut Industri No. 15, Surabaya', 'latitude' => -7.2575, 'longitude' => 112.7521, 'phone' => '031-5552345'],
            ['code' => 'WH-BDG', 'name' => 'Gudang Bandung', 'city' => 'Bandung', 'address' => 'Jl. Soekarno-Hatta No. 25, Bandung', 'latitude' => -6.9175, 'longitude' => 107.6191, 'phone' => '022-5553456'],
            ['code' => 'WH-MDN', 'name' => 'Gudang Medan', 'city' => 'Medan', 'address' => 'Jl. KIM 2 No. 8, Medan', 'latitude' => 3.5952, 'longitude' => 98.6722, 'phone' => '061-5554567'],
            ['code' => 'WH-MKS', 'name' => 'Gudang Makassar', 'city' => 'Makassar', 'address' => 'Jl. Perintis Kemerdekaan KM 12, Makassar', 'latitude' => -5.1477, 'longitude' => 119.4327, 'phone' => '0411-5555678'],
        ];

        $warehouses = [];
        foreach ($data as $w) {
            $warehouses[] = Warehouse::create(array_merge($w, ['is_active' => true]));
        }
        return $warehouses;
    }

    private function seedUsers(array $warehouses): array
    {
        $users = [];

        $users['admin'] = User::create([
            'name' => 'Admin SmartStock',
            'email' => 'admin@smartstock.test',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $users['manager'] = User::create([
            'name' => 'Manager Gudang',
            'email' => 'manager@smartstock.test',
            'password' => Hash::make('password'),
            'role' => 'manager',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $users['staff'] = User::create([
            'name' => 'Staf Gudang Jakarta',
            'email' => 'staff@smartstock.test',
            'password' => Hash::make('password'),
            'role' => 'staff',
            'warehouse_id' => $warehouses[0]->id,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $users['viewer'] = User::create([
            'name' => 'Viewer',
            'email' => 'viewer@smartstock.test',
            'password' => Hash::make('password'),
            'role' => 'viewer',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        return $users;
    }

    private function seedCategories(): array
    {
        $data = [
            ['name' => 'Smartphone', 'description' => 'Perangkat smartphone dan aksesori'],
            ['name' => 'Laptop', 'description' => 'Laptop dan notebook'],
            ['name' => 'Tablet', 'description' => 'Tablet dan iPad'],
            ['name' => 'Audio', 'description' => 'Perangkat audio dan headphone'],
            ['name' => 'Networking', 'description' => 'Router, switch, dan perangkat jaringan'],
            ['name' => 'Aksesori', 'description' => 'Aksesori elektronik umum'],
            ['name' => 'Storage', 'description' => 'Hard disk, SSD, dan flash drive'],
            ['name' => 'Monitor', 'description' => 'Monitor dan display'],
        ];

        $categories = [];
        foreach ($data as $c) {
            $categories[] = Category::create($c);
        }
        return $categories;
    }

    private function seedSuppliers(): array
    {
        $data = [
            ['name' => 'PT Samsung Electronics Indonesia', 'contact_person' => 'Budi Santoso', 'phone' => '021-7891234', 'email' => 'supply@samsung.co.id', 'address' => 'Jl. Jababeka VI Blok J No. 6, Cikarang'],
            ['name' => 'PT Xiaomi Technology Indonesia', 'contact_person' => 'Rina Wijaya', 'phone' => '021-7892345', 'email' => 'partner@xiaomi.co.id', 'address' => 'Jl. TB Simatupang No. 18, Jakarta Selatan'],
            ['name' => 'PT Lenovo Indonesia', 'contact_person' => 'Andi Prabowo', 'phone' => '021-7893456', 'email' => 'distributor@lenovo.co.id', 'address' => 'Menara BCA Lt. 45, Jakarta'],
            ['name' => 'PT Asus Indonesia', 'contact_person' => 'Lisa Hartono', 'phone' => '021-7894567', 'email' => 'supply@asus.co.id', 'address' => 'Jl. Panjang No. 5, Jakarta Barat'],
            ['name' => 'PT JBL Indonesia', 'contact_person' => 'Dimas Nugraha', 'phone' => '021-7895678', 'email' => 'wholesale@jbl.co.id', 'address' => 'Jl. Hayam Wuruk No. 8, Jakarta Pusat'],
        ];

        $suppliers = [];
        foreach ($data as $s) {
            $suppliers[] = Supplier::create($s);
        }
        return $suppliers;
    }

    private function seedProducts(array $categories, array $suppliers): array
    {
        $data = [
            ['sku' => 'SKU-0001-SM', 'name' => 'Samsung Galaxy A54', 'category_id' => $categories[0]->id, 'supplier_id' => $suppliers[0]->id, 'unit' => 'unit', 'purchase_price' => 3500000, 'selling_price' => 4200000, 'minimum_stock' => 20],
            ['sku' => 'SKU-0002-SM', 'name' => 'Samsung Galaxy S24', 'category_id' => $categories[0]->id, 'supplier_id' => $suppliers[0]->id, 'unit' => 'unit', 'purchase_price' => 10000000, 'selling_price' => 12500000, 'minimum_stock' => 10],
            ['sku' => 'SKU-0003-XM', 'name' => 'Xiaomi Redmi Note 13', 'category_id' => $categories[0]->id, 'supplier_id' => $suppliers[1]->id, 'unit' => 'unit', 'purchase_price' => 2200000, 'selling_price' => 2800000, 'minimum_stock' => 30],
            ['sku' => 'SKU-0004-LN', 'name' => 'Lenovo ThinkPad X1 Carbon', 'category_id' => $categories[1]->id, 'supplier_id' => $suppliers[2]->id, 'unit' => 'unit', 'purchase_price' => 18000000, 'selling_price' => 22000000, 'minimum_stock' => 5],
            ['sku' => 'SKU-0005-AS', 'name' => 'Asus ROG Zephyrus G14', 'category_id' => $categories[1]->id, 'supplier_id' => $suppliers[3]->id, 'unit' => 'unit', 'purchase_price' => 20000000, 'selling_price' => 24500000, 'minimum_stock' => 5],
            ['sku' => 'SKU-0006-SM', 'name' => 'Samsung Galaxy Tab S9', 'category_id' => $categories[2]->id, 'supplier_id' => $suppliers[0]->id, 'unit' => 'unit', 'purchase_price' => 8000000, 'selling_price' => 9800000, 'minimum_stock' => 10],
            ['sku' => 'SKU-0007-XM', 'name' => 'Xiaomi Pad 6', 'category_id' => $categories[2]->id, 'supplier_id' => $suppliers[1]->id, 'unit' => 'unit', 'purchase_price' => 3800000, 'selling_price' => 4600000, 'minimum_stock' => 15],
            ['sku' => 'SKU-0008-JB', 'name' => 'JBL Tune 770NC', 'category_id' => $categories[3]->id, 'supplier_id' => $suppliers[4]->id, 'unit' => 'pcs', 'purchase_price' => 1200000, 'selling_price' => 1500000, 'minimum_stock' => 25],
            ['sku' => 'SKU-0009-JB', 'name' => 'JBL Flip 6', 'category_id' => $categories[3]->id, 'supplier_id' => $suppliers[4]->id, 'unit' => 'pcs', 'purchase_price' => 1500000, 'selling_price' => 1900000, 'minimum_stock' => 20],
            ['sku' => 'SKU-0010-AS', 'name' => 'Asus RT-AX86U Router', 'category_id' => $categories[4]->id, 'supplier_id' => $suppliers[3]->id, 'unit' => 'unit', 'purchase_price' => 3200000, 'selling_price' => 3900000, 'minimum_stock' => 10],
            ['sku' => 'SKU-0011-SM', 'name' => 'Samsung EVO 1TB SSD', 'category_id' => $categories[6]->id, 'supplier_id' => $suppliers[0]->id, 'unit' => 'pcs', 'purchase_price' => 1400000, 'selling_price' => 1750000, 'minimum_stock' => 30],
            ['sku' => 'SKU-0012-LN', 'name' => 'Lenovo ThinkVision 27"', 'category_id' => $categories[7]->id, 'supplier_id' => $suppliers[2]->id, 'unit' => 'unit', 'purchase_price' => 3500000, 'selling_price' => 4200000, 'minimum_stock' => 8],
            ['sku' => 'SKU-0013-XM', 'name' => 'Xiaomi USB-C Hub 7in1', 'category_id' => $categories[5]->id, 'supplier_id' => $suppliers[1]->id, 'unit' => 'pcs', 'purchase_price' => 350000, 'selling_price' => 499000, 'minimum_stock' => 40],
            ['sku' => 'SKU-0014-SM', 'name' => 'Samsung 25W Charger', 'category_id' => $categories[5]->id, 'supplier_id' => $suppliers[0]->id, 'unit' => 'pcs', 'purchase_price' => 180000, 'selling_price' => 250000, 'minimum_stock' => 50],
            ['sku' => 'SKU-0015-AS', 'name' => 'Asus ProArt Display 32"', 'category_id' => $categories[7]->id, 'supplier_id' => $suppliers[3]->id, 'unit' => 'unit', 'purchase_price' => 8500000, 'selling_price' => 10200000, 'minimum_stock' => 5],
        ];

        $products = [];
        foreach ($data as $p) {
            $products[] = Product::create(array_merge($p, [
                'description' => 'Produk elektronik berkualitas tinggi dari distributor resmi.',
                'is_active' => true,
            ]));
        }
        return $products;
    }

    private function seedInventoryStocks(array $products, array $warehouses): void
    {
        $quantities = [
            // product index => [warehouse quantities]
            0 => [45, 30, 25, 15, 20],
            1 => [20, 15, 10, 8, 5],
            2 => [60, 50, 40, 35, 25],
            3 => [8, 5, 3, 2, 2],
            4 => [6, 4, 3, 2, 1],
            5 => [15, 12, 8, 5, 5],
            6 => [25, 20, 15, 10, 8],
            7 => [35, 28, 20, 15, 12],
            8 => [30, 25, 18, 12, 10],
            9 => [12, 8, 6, 4, 3],
            10 => [50, 40, 35, 25, 20],
            11 => [10, 8, 5, 3, 2],
            12 => [55, 45, 35, 25, 20],
            13 => [70, 60, 50, 40, 30],
            14 => [6, 4, 3, 2, 1],
        ];

        foreach ($products as $i => $product) {
            foreach ($warehouses as $j => $warehouse) {
                $qty = $quantities[$i][$j] ?? rand(5, 50);
                InventoryStock::create([
                    'product_id' => $product->id,
                    'warehouse_id' => $warehouse->id,
                    'quantity' => $qty,
                ]);
            }
        }
    }

    private function seedStockTransactions(array $products, array $warehouses, array $suppliers, array $users): void
    {
        $admin = $users['admin'];
        $staff = $users['staff'];

        // Sample stock-in transactions
        for ($i = 0; $i < 10; $i++) {
            $product = $products[array_rand($products)];
            $warehouse = $warehouses[array_rand($warehouses)];
            $supplier = $suppliers[array_rand($suppliers)];

            StockTransaction::create([
                'transaction_code' => 'TRX-IN-' . now()->subDays(rand(1, 30))->format('Ymd') . '-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                'type' => 'in',
                'product_id' => $product->id,
                'warehouse_id' => $warehouse->id,
                'supplier_id' => $supplier->id,
                'quantity' => rand(10, 100),
                'transaction_date' => now()->subDays(rand(1, 30)),
                'notes' => 'Barang masuk dari supplier',
                'created_by' => $admin->id,
            ]);
        }

        // Sample stock-out transactions
        for ($i = 0; $i < 8; $i++) {
            $product = $products[array_rand($products)];
            $warehouse = $warehouses[array_rand($warehouses)];

            StockTransaction::create([
                'transaction_code' => 'TRX-OUT-' . now()->subDays(rand(1, 30))->format('Ymd') . '-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                'type' => 'out',
                'product_id' => $product->id,
                'warehouse_id' => $warehouse->id,
                'supplier_id' => null,
                'quantity' => rand(1, 20),
                'transaction_date' => now()->subDays(rand(1, 30)),
                'notes' => 'Barang keluar untuk pengiriman',
                'created_by' => $staff->id,
            ]);
        }
    }

    private function seedStockTransfers(array $products, array $warehouses, array $users): void
    {
        $admin = $users['admin'];

        for ($i = 0; $i < 5; $i++) {
            $product = $products[array_rand($products)];
            $sourceIdx = array_rand($warehouses);
            $destIdx = ($sourceIdx + 1) % count($warehouses);

            StockTransfer::create([
                'transfer_code' => 'TRF-' . now()->subDays(rand(1, 30))->format('Ymd') . '-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                'product_id' => $product->id,
                'source_warehouse_id' => $warehouses[$sourceIdx]->id,
                'destination_warehouse_id' => $warehouses[$destIdx]->id,
                'quantity' => rand(5, 20),
                'transfer_date' => now()->subDays(rand(1, 30)),
                'status' => 'completed',
                'notes' => 'Transfer stok antar gudang',
                'created_by' => $admin->id,
            ]);
        }
    }

    private function seedNotifications(array $users): void
    {
        $notifications = [
            ['type' => 'low_stock', 'title' => 'Stok Rendah', 'message' => 'Stok Samsung Galaxy A54 di Gudang Jakarta di bawah minimum (15 unit)', 'severity' => 'warning'],
            ['type' => 'low_stock', 'title' => 'Stok Kritis', 'message' => 'Stok Asus ROG Zephyrus G14 di Gudang Makassar hanya tersisa 1 unit', 'severity' => 'critical'],
            ['type' => 'transfer', 'title' => 'Transfer Selesai', 'message' => 'Transfer 10 unit Xiaomi Redmi Note 13 dari Jakarta ke Surabaya berhasil', 'severity' => 'info'],
            ['type' => 'stock_in', 'title' => 'Barang Masuk', 'message' => 'Barang masuk 50 unit JBL Tune 770NC ke Gudang Bandung', 'severity' => 'info'],
        ];

        foreach ($notifications as $n) {
            // Create for admin
            Notification::create(array_merge($n, ['user_id' => $users['admin']->id]));
            // Create for manager
            Notification::create(array_merge($n, ['user_id' => $users['manager']->id]));
        }
    }

    private function seedAuditLogs(array $users): void
    {
        $logs = [
            ['action' => 'login', 'module' => 'auth', 'description' => 'User admin logged in'],
            ['action' => 'create', 'module' => 'products', 'description' => 'Created product Samsung Galaxy A54'],
            ['action' => 'stock_in', 'module' => 'stock_transactions', 'description' => 'Stock in 50 units of JBL Tune 770NC to Gudang Jakarta'],
            ['action' => 'stock_out', 'module' => 'stock_transactions', 'description' => 'Stock out 10 units of Samsung Galaxy S24 from Gudang Surabaya'],
            ['action' => 'transfer', 'module' => 'stock_transfers', 'description' => 'Transferred 20 units of Xiaomi Redmi Note 13 from Jakarta to Bandung'],
        ];

        foreach ($logs as $log) {
            AuditLog::create(array_merge($log, [
                'user_id' => $users['admin']->id,
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Seeder',
            ]));
        }
    }
}
