<!DOCTYPE html>
<html><head><meta charset="utf-8"><title>{{ $title }}</title>
<style>body{font-family:sans-serif;font-size:11px}h1{font-size:18px;text-align:center;margin-bottom:2px}.subtitle{text-align:center;font-size:12px;color:#555;margin-bottom:15px}table{width:100%;border-collapse:collapse;margin-top:10px}th,td{border:1px solid #ccc;padding:5px 8px;text-align:left}th{background:#f5f5f5;font-weight:bold}.right{text-align:right}.footer{margin-top:15px;font-size:10px;color:#888;text-align:right}</style>
</head><body>
<h1>{{ $title }}</h1>
<div class="subtitle">PT Maju Bersama Digital — {{ $date }}</div>
<table><thead><tr><th>Kode</th><th>Produk</th><th>Dari</th><th>Ke</th><th class="right">Qty</th><th>Tanggal</th><th>Status</th></tr></thead>
<tbody>
@foreach($data as $t)
<tr><td>{{ $t->transfer_code }}</td><td>{{ $t->product->name }}</td>
<td>{{ $t->sourceWarehouse->name }}</td><td>{{ $t->destinationWarehouse->name }}</td>
<td class="right">{{ number_format($t->quantity) }}</td><td>{{ $t->transfer_date->format('d/m/Y') }}</td>
<td>{{ strtoupper($t->status) }}</td></tr>
@endforeach
</tbody></table>
<div class="footer">Total {{ count($data) }} transfer — Dicetak: {{ now()->format('d/m/Y H:i') }}</div>
</body></html>
