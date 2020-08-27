<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use \App\Models\Project;
use Illuminate\Support\Facades\File;

class DownloadController extends Controller
{
    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Download Pentest Report.
     *
     * @param \App\Models\Project $project
     * @return \Illuminate\Http\Response
     */
    public function report(Project $project)
    {
        $ext = File::mimeType(storage_path('app/reports/' . $project->id));
        if ($ext == 'application/pdf') {
            $ext = '.pdf';
        } else {
            $ext = '.html';
        }

        return Storage::download('reports/' . $project->id, 'Pentest Report - ' . $project->name . $ext);
    }
}
