<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class FileController extends Controller
{
    public function uploadFile(Request $request)
    {
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = time().'_'.$file->getClientOriginalName();
            $files = $file->move(public_path('uploads'), $filename);
            if ($files) {
                return response()->json([
                    'status' => true,
                    'message' => 'file saved successfully',
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'File upload failed',
                ]);
            }

        }
    }

    public function getFilesFromFolder()
    {
        $folderPath = public_path('uploads');

        $files = File::files($folderPath);

        $filePaths = [];
        foreach ($files as $file) {
            $filePaths[] = url('uploads/'.$file->getFilename());
        }
        if ($filePaths) {
            return response()->json([
                'status' => true,
                'message' => 'Data retrieval successfully',
                'data' => $filePaths,
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'No Data/File found',
            ]);
        }
    }
}
