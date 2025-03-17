<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class ImageMoveController extends Controller
{
    public function moveImages()
    {
        $sourcePath = storage_path('app/images/source'); 
        $destinationPath = storage_path('app/images/destination');

        // Ensure source folder exists
        if (!File::exists($sourcePath)) {
            return response()->json(['message' => 'Source folder does not exist.'], 400);
        }

        // Ensure destination folder exists
        if (!File::exists($destinationPath)) {
            File::makeDirectory($destinationPath, 0755, true);
        }

        // Get all files from the source folder
        $files = File::files($sourcePath);

        if (empty($files)) {
            return response()->json(['message' => 'No images found in the source folder.'], 200);
        }

        $movedFiles = [];
        foreach ($files as $file) {
            $newPath = $destinationPath . '/' . $file->getFilename();
            File::move($file->getPathname(), $newPath);
            $movedFiles[] = $file->getFilename();
        }

        //Test My New File

        return response()->json(['message' => 'Images moved successfully.', 'files' => $movedFiles]);
    }
}
