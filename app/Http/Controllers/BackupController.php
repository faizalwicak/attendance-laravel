<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;

class BackupController extends Controller
{
    public function index()
    {
        $filename = "backup-" . date('Y-m-d-Hi') . ".gz";
        $path = storage_path() . "/app/backup/" . $filename;
        $command = "mysqldump --user=" . env('DB_USERNAME') . " --password=" . env('DB_PASSWORD') . " --host=" . env('DB_HOST') . " " . env('DB_DATABASE') . "  | gzip > " . $path;

        $returnVar = NULL;
        $output  = NULL;
        exec($command, $output, $returnVar);

        if (!File::exists($path)) {
            abort(404);
        }

        $file = File::get($path);
        $type = File::mimeType($path);

        $response = Response::make($file, 200);
        $response->header("Content-Type", $type)
            ->header('Content-disposition', 'attachment; filename="' . $filename . '"');

        return $response;
    }
}
