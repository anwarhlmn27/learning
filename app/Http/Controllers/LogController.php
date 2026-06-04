<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class LogController extends Controller
{
    public function index()
    {
        $logPath = storage_path('logs/laravel.log');
        $logs = [];

        if (File::exists($logPath)) {
            // Read the last 2000 lines to avoid memory exhaustion
            $file = new \SplFileObject($logPath, 'r');
            $file->seek(PHP_INT_MAX);
            $totalLines = $file->key();
            $startLine = max(0, $totalLines - 2000);
            
            $file->seek($startLine);
            $content = '';
            while (!$file->eof()) {
                $content .= $file->current();
                $file->next();
            }

            // Split by timestamp pattern: [YYYY-MM-DD HH:MM:SS]
            $entries = preg_split('/(?=\[\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\])/', $content);
            
            foreach ($entries as $entry) {
                if (empty(trim($entry))) continue;
                
                if (preg_match('/^\[(.*?)\] (.*?)\.(.*?): (.*)/s', $entry, $matches)) {
                    $fullMessage = trim($matches[4]);
                    
                    // Extract first line as summary
                    $lines = explode("\n", $fullMessage);
                    $summary = $lines[0];
                    if (strlen($summary) > 120) {
                        $summary = substr($summary, 0, 120) . '...';
                    }

                    $logs[] = [
                        'date' => $matches[1],
                        'environment' => $matches[2],
                        'level' => $matches[3],
                        'summary' => $summary,
                        'full_message' => substr($fullMessage, 0, 5000), // Limit total length sent to view
                    ];
                }
            }
        }
        
        // Reverse so newest is first
        $logs = array_reverse($logs);

        return view('admin.logs.index', compact('logs'));
    }

    public function clear()
    {
        $logPath = storage_path('logs/laravel.log');
        if (File::exists($logPath)) {
            File::put($logPath, '');
        }
        return redirect()->back()->with('success', 'System logs cleared successfully.');
    }
}
