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
        // Determine the correct file input name ('file' for Dropzone, 'upload' for CKEditor)
        $fileInput = $request->hasFile('upload') ? 'upload' : 'file';

        $request->validate([
            $fileInput => 'required|file|max:102400', // Adjusted for up to 100 MB files
            'model_type' => 'sometimes|string', // Type of the model (e.g., 'App\\Models\\Page')
            'model_id' => 'sometimes|integer', // ID of the model instance
        ]);

        $file = $request->file($fileInput);
        $path = $file->store('uploads', 'public');

        // Create a new media instance
        $media = new Media([
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'size' => $file->getSize(),
            'type' => $file->getClientMimeType(),
        ]);

        // Link the media to the model if model_type and model_id are provided
        if ($request->has('model_type') && $request->has('model_id')) {
            $media->mediaable_type = $request->input('model_type');
            $media->mediaable_id = $request->input('model_id');
        }

        $media->save();

        // Check if the request is coming from CKEditor
        if ($request->has('upload')) {
            $url = asset('storage/' . $path);
            return response()->json([
                'uploaded' => 1,
                'url' => $url
            ]);
        }

        // Default response for Dropzone or other requests
        return response()->json([
            'success' => true,
            'fileName' => $media->file_name,
            'url' => asset('storage/' . $path),
            'media_id' => $media->id,
        ]);
    }

    public function destroy(Media $media)
    {
        // Delete the file from storage
        Storage::disk('public')->delete($media->file_path);

        // Delete the media record
        $media->delete();
        return redirect()->back()->with('success', 'Record deleted successfully!');

        // return response()->json(['success' => true]);
    }
}
