<?php

namespace RyanBadger\LaravelAdmin\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use RyanBadger\LaravelAdmin\Models\Media;

class MediaController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:102400', // Adjusted for up to 100 MB files
            'model_type' => 'required|string', // Type of the model (e.g., 'App\Models\Page')
            'model_id' => 'required|integer', // ID of the model instance
        ]);

        $file = $request->file('file');
        $path = $file->store('uploads', 'public');

        // Create a new media instance and link it to the model
        $media = new Media([
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'size' => $file->getSize(),
            'type' => $file->getClientMimeType(),
            'mediaable_id' => $request->input('model_id'),
            'mediaable_type' => $request->input('model_type'),
        ]);

        $media->save();

        return response()->json([
            'success' => true, 
            'fileName' => $media->file_name, 
            'url' => asset('storage/' . $path),
            'media_id' => $media->id
        ]);
    }

    public function destroy(Media $media)
    {
        // Delete the file from storage
        Storage::disk('public')->delete($media->file_path);

        // Delete the media record
        $media->delete();

        return response()->json(['success' => true]);
    }
}
