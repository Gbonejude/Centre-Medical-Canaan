<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>The House Inspection Check - {{ $house->name ?? '' }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Ephesis&display=swap" rel="stylesheet">
    <style>
        @page {
            margin: 25mm 22mm;
            size: A4 portrait;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', 'Arial', sans-serif;
            font-size: 10pt;
            line-height: 1.4;
            color: #333;
            padding: 20px;
        }

        .signature-font {
            font-family: 'Ephesis', cursive;
            font-size: 14pt;
            font-weight: normal;
            color: #000;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .title-box {
            border-top: 2px solid #2c3e50;
            border-bottom: 2px solid #2c3e50;
            padding: 10px 0;
            margin-bottom: 20px;
        }

        .main-title {
            color: #1a4a8e;
            font-size: 14pt;
            font-weight: bold;
            text-transform: uppercase;
        }

        .info-row {
            margin-bottom: 15px;
            font-weight: bold;
        }

        .info-row span {
            margin-right: 20px;
        }

        .dotted-line {
            border-bottom: 1px dotted #000;
            display: inline-block;
            min-width: 100px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        table th,
        table td {
            border: 1px solid #000;
            padding: 6px 8px;
            vertical-align: middle;
        }

        table th {
            background-color: #f8f9fa;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 9pt;
        }

        .col-num {
            width: 5%;
            text-align: center;
        }

        .col-items {
            width: 35%;
        }

        .col-obs {
            width: 60%;
        }

        .footer {
            margin-top: 40px;
            font-size: 9.5pt;
        }

        .signature-row {
            display: table;
            width: 100%;
        }

        .signature-cell {
            display: table-cell;
            width: 50%;
        }

        .sig-line {
            display: inline-block;
            border-bottom: 1px solid #000;
            min-width: 180px;
            height: 22px;
            vertical-align: bottom;
            margin: 0 5px;
            text-align: center;
            line-height: 14px;
        }

        /* ── Page 2 – Images ── */
        .page-break {
            page-break-before: always;
        }

        .images-page-title {
            text-align: center;
            color: #1a4a8e;
            font-size: 16pt;
            font-weight: bold;
            text-transform: uppercase;
            border-top: 2px solid #2c3e50;
            border-bottom: 2px solid #2c3e50;
            padding: 8px 0;
            margin-bottom: 10px;
        }

        .images-subtitle {
            text-align: center;
            font-size: 9pt;
            color: #666;
            margin-bottom: 16px;
        }

        .photo-section {
            margin-bottom: 16px;
            page-break-inside: avoid;
        }

        .photo-item-name {
            font-weight: bold;
            font-size: 9.5pt;
            background-color: #e8edf5;
            border: 1px solid #b0bfd6;
            padding: 4px 8px;
            margin-bottom: 5px;
            color: #1a4a8e;
        }

        .photo-grid {
            display: table;
            width: 100%;
            border-spacing: 0;
        }

        .photo-col {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding: 5px;
            border: 1px solid #dde;
            text-align: center;
        }

        .photo-col+.photo-col {
            border-left: none;
        }

        .photo-label {
            font-size: 8.5pt;
            font-weight: bold;
            margin-bottom: 5px;
            padding: 2px 8px;
            border-radius: 3px;
            display: inline-block;
        }

        .label-before {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffc107;
        }

        .label-after {
            background-color: #d1e7dd;
            color: #0f5132;
            border: 1px solid #198754;
        }

        .img-container {
            width: 100%;
            height: 200px; /* strict height for domPDF */
            overflow: hidden;
            border: 1px solid #ccc;
            border-radius: 3px;
            margin-top: 4px;
            text-align: center;
        }

        .photo-img {
            max-width: 100%;
            max-height: 100%;
            height: auto;
            width: auto;
            display: inline-block;
            vertical-align: middle;
        }

        .no-photo-box {
            width: 100%;
            height: 80px;
            background-color: #f8f9fa;
            border: 1px dashed #ccc;
            border-radius: 3px;
            margin-top: 4px;
            text-align: center;
            vertical-align: middle;
            color: #aaa;
            font-size: 8pt;
            font-style: italic;
            display: table;
        }

        .no-photo-inner {
            display: table-cell;
            vertical-align: middle;
        }
    </style>
</head>

<body>

    {{-- ══════════════ PAGE 1 – Inspection Checklist ══════════════ --}}
    <div class="header">
        <div class="title-box">
            <div class="main-title">THE HOUSE INSPECTION CHECK – 520 C</div>
        </div>
    </div>

    <div class="info-row">
        House Name: {{ $house->name ?? '................................................' }}
        &nbsp;&nbsp;&nbsp;
        Date
        {{ $inspection->inspection_date ? \Carbon\Carbon::parse($inspection->inspection_date)->format('m / d') : '......... / .........' }}
        / {{ $inspection->year ?? '2025' }}
    </div>

    <table>
        <thead>
            <tr>
                <th class="col-num">№</th>
                <th class="col-items">ITEMS</th>
                <th class="col-obs">OBSERVATIONS</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $index => $item)
                <tr>
                    <td class="col-num">{{ $index + 1 }}</td>
                    <td class="col-items">{{ $item->item_name }}</td>
                    <td class="col-obs">
                        @if($item->is_good)
                            is good
                        @else
                            {{ $item->observation }}
                        @endif
                    </td>
                </tr>
            @endforeach

            {{-- Add empty rows to fill space --}}
            @for($i = count($items); $i < 22; $i++)
                <tr>
                    <td class="col-num">{{ $i + 1 }}</td>
                    <td class="col-items"></td>
                    <td class="col-obs"></td>
                </tr>
            @endfor
        </tbody>
    </table>

    <div class="footer">
        <div class="signature-row">
            <div class="signature-cell">
                Viviane Atchou PD <span class="sig-line"></span>
            </div>
            <div class="signature-cell" style="width: auto; white-space: nowrap; padding: 0 8px;">
                Or &nbsp; House Manager <span class="sig-line signature-font">Dzreke Simon</span>
            </div>
        </div>
    </div>


    {{-- ══════════════ PAGE 2 – Images ══════════════ --}}
    @if(!empty($itemsWithPhotos))
        <div class="page-break">

            <div class="images-page-title">THE IMAGES</div>
            <div class="images-subtitle">
                House: {{ $house->name ?? '' }} &nbsp;|&nbsp; {{ $period }}
            </div>

            @foreach($itemsWithPhotos as $photoItem)
                <div class="photo-section">

                    <div class="photo-item-name">N° {{ $photoItem['index'] ?? $loop->iteration }} - {{ $photoItem['name'] }}</div>

                    <div class="photo-grid">

                        {{-- Column DEFECTIVE --}}
                        <div class="photo-col">
                            <span class="photo-label label-before">DEFECTIVE</span>
                            @if($photoItem['photo_before'])
                                <div class="img-container">
                                    <img class="photo-img" src="{{ $photoItem['photo_before'] }}" alt="Defective" />
                                </div>
                            @else
                                <div class="no-photo-box">
                                    <div class="no-photo-inner">No photo</div>
                                </div>
                            @endif
                        </div>

                        {{-- Column SOLVED --}}
                        <div class="photo-col">
                            <span class="photo-label label-after">SOLVED</span>
                            @if($photoItem['photo_after'])
                                <div class="img-container">
                                    <img class="photo-img" src="{{ $photoItem['photo_after'] }}" alt="Solved" />
                                </div>
                            @else
                                <div class="no-photo-box">
                                    <div class="no-photo-inner">No photo</div>
                                </div>
                            @endif
                        </div>

                    </div>
                </div>
            @endforeach

        </div>
    @endif

</body>

</html>