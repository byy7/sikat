<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Support\Facades\Storage;

class DownloadController extends Controller
{
    public function __invoke(string $id)
    {
        $report = Report::find(decrypt($id));
        $photoPath = Storage::path($report->photo);

        return response()->download($photoPath);
    }
}
