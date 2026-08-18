<?php

namespace App\Http\Controllers;

use App\Models\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class Commands extends Controller
{

    public function get_commands(){
        $commands = Command::all();

        $datas = [];

        foreach ($commands as $command) {
            $datas[] = [
                'code' => $command->code,
                'command' => $command->command,
                'description' => $command->description,
                'image' => Storage::disk('public')->url('/images/' . $command->image),
            ];
        }

        return response()->json([
            'success' => true,
            'commands' => $datas,
        ]);
    }

    public function execute(Request $request)
    {
        $code = $request->input('code');

        $command = Command::where('code', $code)->first();

        if ($command) {
            shell_exec($command->command);
            return response()->json(['success' => true]);
        } else {
            return response()->json(['success' => false]);
        }
    }

    public function create_command(Request $request)
    {
        $request->validate([
            'command' => ['required', 'string'],
            'description' => ['required', 'string'],
            'file' => ['nullable', 'image', 'mimes:png'],
        ]);

        $cmd = $request->input('command');
        $description = $request->input('description');
        $file = $request->file('file');

        $code = uniqid();
        $image = null;

        if ($file) {
            $image = $code . '.png';

            Storage::disk('public')->putFileAs(
                'images',
                $file,
                $image,
            );
        }

        Command::create([
            'command' => $cmd,
            'code' => $code,
            'description' => $description,
            'image' => $image,
        ]);

        return response()->json([
            'success' => true
        ]);
    }

}
