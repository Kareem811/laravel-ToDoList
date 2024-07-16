<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TodoController extends Controller
{
    public function index()
    {
        return Todo::all();
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'description' => 'nullable|string',
            'file' => 'nullable|mimes:pdf|max:2048'
        ]);

        $todo = new Todo();
        $todo->title = $request->title;
        $todo->description = $request->description;

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filePath = $file->store('files');
            $todo->file = $filePath;
        }

        $todo->save();

        return response()->json($todo, 201);
    }

    public function show($id)
    {
        return Todo::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string',
            'description' => 'nullable|string',
            'file' => 'nullable|mimes:pdf|max:2048'
        ]);

        $todo = Todo::findOrFail($id);
        $todo->title = $request->title;
        $todo->description = $request->description;

        if ($request->hasFile('file')) {
            if ($todo->file) {
                Storage::delete($todo->file);
            }
            $file = $request->file('file');
            $filePath = $file->store('files');
            $todo->file = $filePath;
        }

        $todo->save();

        return response()->json($todo);
    }

    public function destroy($id)
    {
        $todo = Todo::findOrFail($id);
        if ($todo->file) {
            Storage::delete($todo->file);
        }
        $todo->delete();
        return response()->json(null, 204);
    }
}
