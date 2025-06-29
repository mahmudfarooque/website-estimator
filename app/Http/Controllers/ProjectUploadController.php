<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests; // <-- 1. IMPORT THE TRAIT

class ProjectUploadController extends Controller
{
    use AuthorizesRequests; // <-- 2. USE THE TRAIT

    public function store(Request $request, Project $project)
    {
        // 1. Authorization: This will now work correctly
        $this->authorize('update', $project);

        // 2. Validation: Check the file and the project's upload count
        $request->validate([
            'file' => [
                'required',
                'file',
                'mimes:jpg,jpeg,png,pdf,doc,docx,zip', // Allowed file types
                'max:5120', // Max file size in kilobytes (5MB)
            ],
        ]);

        if ($project->uploads()->count() >= 5) {
            return back()->withErrors(['file' => 'You cannot upload more than 5 files.']);
        }

        // 3. Store the file
        $uploadedFile = $request->file('file');
        $path = $uploadedFile->store('uploads/project_' . $project->id, 'public');

        // 4. Create a record in the database
        $project->uploads()->create([
            'original_filename' => $uploadedFile->getClientOriginalName(),
            'stored_filename' => basename($path),
            'file_path' => $path,
            'mime_type' => $uploadedFile->getMimeType(),
            'size' => $uploadedFile->getSize(),
        ]);

        // 5. Redirect back with a success message
        return back()->with('success', 'File uploaded successfully!');
    }

    public function destroy(Request $request, ProjectUpload $upload)
    {
        // 1. Authorization: This will now work correctly
        $this->authorize('update', $upload->project);

        // 2. Delete the physical file from storage
        Storage::disk('public')->delete($upload->file_path);

        // 3. Delete the record from the database
        $upload->delete();

        // 4. Redirect back
        return back()->with('success', 'File deleted successfully.');
    }
}