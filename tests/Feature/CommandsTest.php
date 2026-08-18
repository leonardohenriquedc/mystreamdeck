<?php

use App\Http\Controllers\Commands;
use App\Models\Command;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

test('create', function () {
    Route::post('/commands', [Commands::class, 'create_command'])->middleware('web');

    Storage::fake('public');

    $file = UploadedFile::fake()->create('icon.png', 1, 'image/png');

    $response = $this->post('/commands', [
        'command' => 'ls -la',
        'description' => 'List files in the current directory',
        'file' => $file,
    ]);

    $response->assertOk();
    $response->assertJson(['success' => true]);

    expect(Command::count())->toBe(1);

    $command = Command::first();
    expect($command->command)->toBe('ls -la');
    expect($command->description)->toBe('List files in the current directory');
    expect($command->code)->not->toBeNull();
    expect($command->image)->toBe($command->code . '.png');

    Storage::disk('public')->assertExists('images/' . $command->code . '.png');
});

test('get_commands', function () {
    Route::get('/commands', [Commands::class, 'get_commands'])->middleware('web');

    $response = $this->get('/commands');

    $response->assertOk();
    $response->assertJson([]);
});
