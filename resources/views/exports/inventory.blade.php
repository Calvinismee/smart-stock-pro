<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>{{ $title }}</title>
<style>
body { font-family: sans-serif; font-size: 11px; }
h1 { font-size: 18px; text-align: center; margin-bottom: 2px; }
.subtitle { text-align: center; font-size: 12px; color: #555; margin-bottom: 15px; }
table { width: 100%; border-collapse: collapse; margin-top: 10px; }
th, td { border: 1px solid #ccc; padding: 5px 8px; text-align: left; }
th { background: #f5f5f5; font-weight: bold; }
.right { text-align: right; }
.footer { margin-top: 15px; font-size: 10px; color: #888; text-align: right; }
</style>
</head>
<body>
<h1>{{ $title }}</h1>
<div class="subtitle">PT Maju Bersama Digital — {{ $date }}</div>
<table>
<thead><tr><th>SKU</th><th>Produk</th><th>Kategori</th><th>Gudang</th><th class="right">Stok</th><th class="right">Min</th><th class="right">Harga Jual</th></tr></thead>
<tbody>
@foreach($data as $row)
<tr>
<td>{{ $row->sku }}</td><td>{{ $row->product }}</td><td>{{ $row->category }}</td>
<td>{{ $row->warehouse }}</td><td class="right">{{ number_format($row->quantity) }}</td>
<td class="right">{{ number_format($row->minimum_stock) }}</td>
<td class="right">Rp {{ number_format($row->selling_price,0,',','.') }}</td>
</tr>
@endforeach
</tbody>
</table>
<div class="footer">Total {{ count($data) }} baris — Dicetak: {{ now()->format('d/m/Y H:i') }}</div>
</body>
</html>
