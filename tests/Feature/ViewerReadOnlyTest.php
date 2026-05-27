<?php

use App\Models\User;
use App\Models\Product;

beforeEach(function () {
    $this->viewer = User::factory()->viewer()->create();
    $this->product = Product::factory()->create();
});

test('TC-21: Viewer can access product list', function () {
    $this->actingAs($this->viewer)->get('/products')->assertStatus(200);
});

test('TC-22: Viewer can access product detail', function () {
    $this->actingAs($this->viewer)->get('/products/' . $this->product->id)->assertStatus(200);
});

test('TC-23: Viewer can access inventory stock monitoring', function () {
    $this->actingAs($this->viewer)->get('/inventory-stocks')->assertStatus(200);
});

test('TC-24: Viewer can access reports page', function () {
    $this->actingAs($this->viewer)->get('/reports')->assertStatus(200);
});

test('TC-25: Viewer can access warehouse map', function () {
    $this->actingAs($this->viewer)->get('/warehouse-map')->assertStatus(200);
});

test('TC-26: Viewer cannot access product create page', function () {
    $this->actingAs($this->viewer)->get('/products/create')->assertStatus(403);
});

test('TC-27: Viewer cannot create product', function () {
    $this->actingAs($this->viewer)->post('/products', [])->assertStatus(403);
});

test('TC-28: Viewer cannot update product', function () {
    $this->actingAs($this->viewer)->patch('/products/' . $this->product->id, [])->assertStatus(403);
});

test('TC-29: Viewer cannot delete product', function () {
    $this->actingAs($this->viewer)->delete('/products/' . $this->product->id)->assertStatus(403);
});

test('TC-30: Viewer cannot create category, warehouse, supplier', function () {
    $this->actingAs($this->viewer)->post('/categories', [])->assertStatus(403);
    $this->actingAs($this->viewer)->post('/warehouses', [])->assertStatus(403);
    $this->actingAs($this->viewer)->post('/suppliers', [])->assertStatus(403);
});

test('TC-31: Viewer cannot access stock-in/out forms and transactions', function () {
    $this->actingAs($this->viewer)->post('/stock-transactions/store-in', [])->assertStatus(403);
    $this->actingAs($this->viewer)->post('/stock-transactions/store-out', [])->assertStatus(403);
});

test('TC-32: Viewer cannot create warehouse transfer', function () {
    $this->actingAs($this->viewer)->post('/stock-transfers', [])->assertStatus(403);
});

test('TC-33: Viewer cannot access import page', function () {
    $this->actingAs($this->viewer)->get('/import')->assertStatus(403);
});

test('TC-34: Viewer cannot access user management, audit log, error log', function () {
    $this->actingAs($this->viewer)->get('/users')->assertStatus(403);
    $this->actingAs($this->viewer)->get('/audit-logs')->assertStatus(403);
    $this->actingAs($this->viewer)->get('/error-logs')->assertStatus(403);
});
