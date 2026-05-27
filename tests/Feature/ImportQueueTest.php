<?php

use App\Models\User;
use App\Jobs\ImportProductsJob;
use Illuminate\Support\Facades\Queue;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('TC-64: Admin can access import page', function () {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin)->get('/import')->assertStatus(200);
});

test('TC-65: Manajer Gudang can access import page', function () {
    $manager = User::factory()->manager()->create();
    $this->actingAs($manager)->get('/import')->assertStatus(200);
});

test('TC-66: Staf Gudang and Viewer cannot access import page', function () {
    $staff = User::factory()->staff()->create();
    $this->actingAs($staff)->get('/import')->assertStatus(403);
    
    $viewer = User::factory()->viewer()->create();
    $this->actingAs($viewer)->get('/import')->assertStatus(403);
});

test('TC-67: Admin can upload valid product CSV and dispatches job', function () {
    Queue::fake();
    Storage::fake('private');
    
    $admin = User::factory()->admin()->create();
    
    $file = UploadedFile::fake()->createWithContent('products.csv', "name,sku,category_id,description,purchase_price,selling_price,minimum_stock\nTest Product,SKU-001,1,Desc,100,150,10");

    $this->actingAs($admin)->post('/import/products', [
        'file' => $file,
    ])->assertRedirect();
    
    Queue::assertPushed(ImportProductsJob::class);
});

test('TC-68: Invalid import file is rejected', function () {
    $admin = User::factory()->admin()->create();
    
    // PDF is invalid, only csv,xls,xlsx allowed
    $file = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');

    $this->actingAs($admin)->post('/import/products', [
        'file' => $file,
    ])->assertSessionHasErrors('file');
});
