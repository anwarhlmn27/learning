@extends('layouts.admin')

@section('title', 'System Activity Logs')

@section('content')
<div class="card">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
        <h3 style="margin: 0; font-size: 1.25rem;">System Activity Logs</h3>
        <div>
            <form action="{{ route('logs.clear') }}" method="POST" onsubmit="return confirm('Are you sure you want to clear all system logs? This action cannot be undone.')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger" style="background-color: #ef4444; color: white; border: none; padding: 0.5rem 1rem; border-radius: 4px; cursor: pointer;">
                    <i>🗑️</i> Clear Logs
                </button>
            </form>
        </div>
    </div>
    <div class="card-body">
        <div style="background-color: #eff6ff; border-left: 4px solid #3b82f6; padding: 1rem; margin-bottom: 1.5rem; border-radius: 4px;">
            <p style="margin: 0; font-size: 0.875rem; color: #1e3a8a;">
                <strong>Info:</strong> Menampilkan log aktivitas sistem (Error, Warning, Info) dari file <code>storage/logs/laravel.log</code>. 
                Sistem logging ini sangat ringan karena membaca langsung dari file teks (file-based) dan tidak menggunakan database, 
                sehingga <strong>tidak akan memberatkan kinerja database maupun memori server Anda</strong>.
            </p>
        </div>

        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; font-size: 0.875rem;">
                <thead>
                    <tr style="background-color: #f8fafc; border-bottom: 2px solid #e2e8f0; text-align: left;">
                        <th style="padding: 0.75rem 1rem;">{{ __('Time') }}</th>
                        <th style="padding: 0.75rem 1rem;">{{ __('Environment') }}</th>
                        <th style="padding: 0.75rem 1rem;">{{ __('Level') }}</th>
                        <th style="padding: 0.75rem 1rem;">Message / Error</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        @php
                            $levelColor = match($log['level']) {
                                'ERROR' => '#fee2e2; color: #991b1b',
                                'WARNING' => '#fef3c7; color: #92400e',
                                'INFO' => '#e0f2fe; color: #075985',
                                'DEBUG' => '#f1f5f9; color: #475569',
                                default => '#f3f4f6; color: #1f2937'
                            };
                        @endphp
                        <tr style="border-bottom: 1px solid #e2e8f0;">
                            <td style="padding: 0.75rem 1rem; white-space: nowrap;">{{ $log['date'] }}</td>
                            <td style="padding: 0.75rem 1rem;">{{ $log['environment'] }}</td>
                            <td style="padding: 0.75rem 1rem;">
                                <span style="background: {{ $levelColor }}; padding: 0.2rem 0.5rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600;">
                                    {{ $log['level'] }}
                                </span>
                            </td>
                            <td style="padding: 0.75rem 1rem; font-family: monospace; max-width: 500px;">
                                <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 1rem;">
                                    <span style="word-break: break-all; overflow: hidden; text-overflow: ellipsis; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">{{ $log['summary'] }}</span>
                                    <button type="button" onclick="showLogDetail('log-detail-{{ $loop->index }}')" style="background: #f1f5f9; border: 1px solid #cbd5e1; border-radius: 4px; padding: 4px 8px; cursor: pointer; color: #475569; display: flex; align-items: center; gap: 4px; flex-shrink: 0; font-size: 0.75rem; font-weight: 600; transition: background 0.2s;">
                                        <span>🔍</span> Detail
                                    </button>
                                </div>
                                <div id="log-detail-{{ $loop->index }}" style="display: none;">{{ $log['full_message'] }}</div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="padding: 2rem; text-align: center; color: #6b7280;">
                                <i>📭</i> No system logs found. The system is operating normally.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Log Detail Modal -->
<div id="logModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.6); z-index: 1000; align-items: center; justify-content: center; padding: 1rem;">
    <div class="card" style="width: 100%; max-width: 900px; max-height: 90vh; display: flex; flex-direction: column; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; background: white; z-index: 10;">
            <h3 style="margin: 0; font-size: 1.1rem; color: #1e293b;">Log Details</h3>
            <button onclick="document.getElementById('logModal').style.display = 'none'" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: #6b7280; padding: 0;">&times;</button>
        </div>
        <div class="card-body" style="overflow-y: auto; background: #0f172a; color: #e2e8f0; font-family: 'Courier New', Courier, monospace; font-size: 0.85rem; padding: 1.25rem; flex-grow: 1;">
            <pre id="logModalContent" style="margin: 0; white-space: pre-wrap; word-break: break-word; line-height: 1.5;"></pre>
        </div>
        <div style="padding: 1rem; border-top: 1px solid #e2e8f0; text-align: right; background: #f8fafc; border-radius: 0 0 8px 8px;">
            <button type="button" class="btn btn-outline" onclick="document.getElementById('logModal').style.display = 'none'" style="background: white;">Close</button>
        </div>
    </div>
</div>

<script>
    function showLogDetail(elementId) {
        const content = document.getElementById(elementId).innerText;
        document.getElementById('logModalContent').innerText = content;
        document.getElementById('logModal').style.display = 'flex';
    }

    // Close modal on click outside
    window.addEventListener('click', function(e) {
        const modal = document.getElementById('logModal');
        if (e.target === modal) {
            modal.style.display = 'none';
        }
    });
</script>
@endsection
