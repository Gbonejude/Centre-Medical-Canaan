<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CCS Individual Record Review - {{ $customer->lastname }}, {{ $customer->firstname }} ({{ $customer->phone }}) - {{ $period }}</title>
    <style>
        @page {
            margin: 8mm 6mm;
            size: A4 portrait;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', 'Arial', sans-serif;
            font-size: 9pt;
            line-height: 1.3;
            color: #000;
            margin: 0;
        }

        .page {
            width: 100%;
            /* padding: 100px !important; */
            /* padding-left: 20px !important;
            padding-right: 500px !important; */
            max-width: 100%;
            overflow: hidden;
        }

        /* Header */
        .main-title {
            text-align: center;
            font-size: 12pt;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .header-info {
            display: table;
            width: 100%;
            margin-bottom: 8px;
            padding-left: 20px !important;
            padding-right: 20px !important;
        }

        .header-left {
            display: table-cell;
            text-align: left;
            width: 50%;
        }

        .header-right {
            display: table-cell;
            text-align: right;
            width: 50%;
        }

        /* Main table */
        .main-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            table-layout: fixed;
            padding-left: 20px !important;
            padding-right: 20px !important;
        }

        .main-table th,
        .main-table td {
            border: 1px solid #000;
            padding: 4px 5px;
            vertical-align: top;
            font-size: 8pt;
            word-wrap: break-word;
            overflow-wrap: break-word;
            hyphens: auto;
        }

        .main-table th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: left;
        }

        /* Column widths */
        .field-col {
            width: 13%;
        }

        .observation-col {
            width: 38%;
        }

        .followup-col {
            width: 39%;
        }

        /* Description text */
        .description-text {
            font-size: 8pt;
            white-space: pre-wrap;
            word-break: break-word;
            line-height: 1.4;
        }

        /* Signature section */
        .signature-section {
            margin-top: 15px;
            padding-left: 20px !important;
            /* padding-right: 60px !important; */
        }

        .signature-line {
            margin-bottom: 5px;
            font-size: 9pt;
        }

        .signature-underline {
            display: inline-block;
            border-bottom: 1px solid #000;
            min-width: 200px;
        }

        /* Footer */
        .footer {
            margin-top: 20px;
            padding-top: 10px;
            font-size: 9pt;
        }

        /* Header styling */
        .header-left,
        .header-right {
            font-size: 9pt;
        }

        /* Ensure table headers are visible */
        .main-table thead th {
            font-size: 9pt;
        }
    </style>
</head>
<body>
    <div class="page">
        <!-- Main Title -->
        <div class="main-title">CCS INDIVIDUAL RECORD REVIEW</div>

        <!-- Header Info -->
        <div class="header-info">
            <div class="header-left">
                Individual Name: {{ $customer->lastname }}, {{ $customer->firstname }} ({{ $customer->phone }})
            </div>
            <div class="header-right">
                Month/Year {{ $period }}
            </div>
        </div>

        <!-- Report Table -->
        <table class="main-table">
            <thead>
                <tr>
                    <th class="field-col"></th>
                    <th class="observation-col">Observation</th>
                    <th class="followup-col">Follow Up Need</th>
                </tr>
            </thead>
            <tbody>
                @if($reports->count() > 0)
                    @foreach($reports as $report)
                    <tr>
                        <td class="field-col">
                            <strong>{{ strtoupper($report->report_field ?? 'N/A') }}</strong>
                        </td>
                        <td class="observation-col">
                            <div class="description-text">{{ $report->observation ?? 'N/A' }}</div>
                        </td>
                        <td class="followup-col">
                            @if($report->follow_up_need)
                                <div class="description-text">{{ $report->follow_up_need }}</div>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                @else
                <tr>
                    <td colspan="3" style="text-align: center; padding: 20px;">
                        No reports have been recorded for this period.
                    </td>
                </tr>
                @endif
            </tbody>
        </table>

        <!-- Signature Section -->
        <div class="signature-section">
            <div class="signature-line">
                <strong>Staff:</strong>____{{ $reports->first()?->createdBy?->firstname }} {{ $reports->first()?->createdBy?->lastname }}___________
            </div>
        </div>
    </div>
</body>
</html>
