@extends('layouts.app')
<head>
    <meta charset="UTF-8">
    <title>Activity Details - {{ $activity->activity_title_en ?? 'Activity' }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    {{-- Simple styling, no framework required --}}
    <style>
        :root {
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            color-scheme: light dark;
        }

        .page {
            width: 80%;
            margin: auto;
        }

        .card {
            background: #ffffff;
            border-radius: 12px;
            padding: 1.5rem 1.75rem;
            box-shadow:
                0 10px 20px rgba(0, 0, 0, 0.04),
                0 2px 6px rgba(0, 0, 0, 0.02);
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            gap: 1rem;
        }

        .title {
            font-size: 1.25rem;
            font-weight: 600;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.6rem;
            border-radius: 999px;
            font-size: 0.75rem;
            font-weight: 500;
            background: #e5f6ff;
            color: #055160 !important;
        }

        .meta-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 0.75rem 1.5rem;
            margin-top: 1rem;
        }

        .field-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #6b7280;
            margin-bottom: 0.15rem;
        }

        .field-value {
            font-size: 0.95rem;
            color: #111827;
            word-break: break-word;
        }

        .muted {
            color: #9ca3af;
            font-style: italic;
        }

        .divider {
            height: 1px;
            background: #e5e7eb;
            margin: 1rem 0 1.25rem;
        }

        .message {
            margin-top: 0.75rem;
            font-size: 0.9rem;
            color: #16a34a;
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
        }

        .message::before {
            content: "✔";
            font-size: 0.95rem;
        }

        @media (max-width: 480px) {
            .card {
                padding: 1.25rem 1.1rem;
            }
        }
    </style>
</head>
@section('content')

<div class="page">
    <div class="card">
        <div class="card-header">
            <div>
                <div class="title">
                    {{ $activity->activity_title_en ?? 'Untitled Activity' }}
                </div>
                @if(!empty($activity->activity_type))
                    <div class="badge">
                        {{ $activity->activity_type }}
                    </div>
                @endif
            </div>

            @if(!empty($activity->external_id))
                <div style="text-align:right; font-size:0.85rem; color:#6b7280;">
                    <div>External ID</div>
                    <div style="font-weight:600;">
                        {{ $activity->external_id }}
                    </div>
                </div>
            @endif
        </div>

        <div class="divider"></div>

        <div class="meta-grid">
            <div>
                <div class="field-label">Content Network</div>
                <div class="field-value">
                    {{ $activity->content_network ?? '—' }}
                </div>
            </div>

            <div>
                <div class="field-label">Folder Name</div>
                <div class="field-value">
                    @if(!empty($activity->folder_name))
                        {{ $activity->folder_name }}
                    @else
                        <span class="muted">None</span>
                    @endif
                </div>
            </div>

            <div>
                <div class="field-label">Start Date</div>
                <div class="field-value">
                    {{ \Carbon\Carbon::parse($activity->start_date)->format('Y-m-d') ?? '—' }}
                </div>
            </div>

            <div>
                <div class="field-label">End Date</div>
                <div class="field-value">
                    {{ \Carbon\Carbon::parse($activity->end_date)->format('Y-m-d') ?? '—' }}
                </div>
            </div>

            <div>
                <div class="field-label">Activity ID</div>
                <div class="field-value">
                    {{ $activity->activity_id ?? '—' }}
                </div>
            </div>

            <div>
                <div class="field-label">Created At</div>
                <div class="field-value">
                    {{ optional($activity->created_at)->format('Y-m-d H:i') ?? '—' }}
                </div>
            </div>

            <div>
                <div class="field-label">Updated At</div>
                <div class="field-value">
                    {{ optional($activity->updated_at)->format('Y-m-d H:i') ?? '—' }}
                </div>
            </div>
        </div>

        @isset($message)
            <div class="message">
                {{ $message }}
            </div>
        @endisset
    </div>
</div>
@endsection