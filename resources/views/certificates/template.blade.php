<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <style>
        @page {
            margin: 0mm;
            padding: 0mm;
            size: 297mm 210mm;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html,
        body {
            width: 297mm;
            height: 210mm;
            margin: 0;
            padding: 0;
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 12px;
        }

        .certificate-container {
            width: 297mm;
            height: 210mm;
            background: #3b5998;
            position: absolute;
            top: 0;
            left: 0;
        }

        .certificate-inner {
            width: 287mm;
            height: 200mm;
            margin: 5mm;
            background: #ffffff;
            border: 3px solid #28a745;
            position: relative;
        }

        .decorative-border {
            position: absolute;
            top: 8mm;
            left: 8mm;
            right: 8mm;
            bottom: 8mm;
            border: 5px solid #28a745;
        }

        .corner-ornament {
            position: absolute;
            width: 14mm;
            height: 14mm;
            background: #28a745;
            border-radius: 0%;
        }

        .corner-ornament.top-left {
            top: -5.7mm;
            left: -5.7mm;
        }

        .corner-ornament.top-right {
            top: -5.7mm;
            right: -5.7mm;
        }

        .corner-ornament.bottom-left {
            bottom: -5.8mm;
            left: -5.8mm;
        }

        .corner-ornament.bottom-right {
            bottom: -5.8mm;
            right: -5.8mm;
        }

        .watermark {
            position: absolute;
            top: 105mm;
            left: 148.5mm;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 200px;
            color: rgba(59, 89, 152, 0.05);
            font-weight: bold;
            z-index: 1;
        }

        .header {
            text-align: center;
            padding-top: 15mm;
            padding-left: 20mm;
            padding-right: 20mm;
            padding-bottom: 10mm;
            position: relative;
            z-index: 2;
        }

        .logo-section {
            margin-bottom: 12mm;
        }

        .logo-image {
        width: 20mm !important;
        height: 20mm !important;
        margin: 0 auto !important;
        border-radius: 50% !important;
        object-fit: cover !important;
        display: block !important;
        max-width: 20mm !important;
        max-height: 20mm !important;
        }
        .logo-placeholder {
            width: 20mm;
            height: 20mm;
            margin: 0 auto;
            background: #3b5998;
            border-radius: 50%;
            color: white;
            font-size: 12px;
            font-weight: bold;
            line-height: 20mm;
            text-align: center;
        }

        .certificate-title {
            font-size: 80px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 5mm;
            text-transform: uppercase;
            /* letter-spacing: 0px; */
        }

        .subtitle {
            font-size: 25px;
            color: #7f8c8d;
            margin-bottom: 8mm;
        }

        .content {
            padding-left: 25mm;
            padding-right: 25mm;
            text-align: center;
            position: relative;
            z-index: 2;
        }

        .certification-text {
            font-size: 30px;
            color: #34495e;
            line-height: 1.4;
            margin-top: 4mm;
            margin-bottom: 4mm;
        }

        .recipient-name {
            font-size: 45px;
            font-weight: bold;
            color: #2c3e50;
            padding: 8mm 15mm;
            border-bottom: 2px solid #2c3e50;
            ;
            display: inline-block;
            margin-top: 4mm;
            margin-bottom: 6mm;
            min-width: 80mm;
        }

        .course-details {
            margin-top: 8mm;
            margin-bottom: 8mm;
        }

        .course-name {
            font-size: 50px;
            font-weight: bold;
            color: #3b5998;
            margin-bottom: 4mm;
        }

        .footer {
            position: absolute;
            bottom: 15mm;
            left: 25mm;
            right: 25mm;
            height: 25mm;
        }

        .footer-table {
            width: 100%;
            border-collapse: collapse;
        }

        .footer-cell {
            width: 33.33%;
            text-align: center;
            vertical-align: bottom;
        }

        .signature-line {
            width: 35mm;
            height: 1px;
            background: #2c3e50;
            margin: 3mm auto 3mm;
        }

        .signature-label {
            font-size: 25px;
            color: #7f8c8d;
        }

        .signature-name {
        font-size: 25px;
        color: #000000;
        margin-bottom: 2mm;
        }

        .date {
            font-size: 25px;
            color: #000000;
            font-weight: bold;
            margin-bottom: 2mm;
        }
    </style>
</head>

<body>
    <div class="certificate-container">
        <div class="certificate-inner">
            <div class="decorative-border"></div>

            <div class="corner-ornament top-left"></div>
            <div class="corner-ornament top-right"></div>
            <div class="corner-ornament bottom-left"></div>
            <div class="corner-ornament bottom-right"></div>

            <div class="watermark">CERTIFIED</div>

            <div class="header">

                <h1 class="certificate-title">Certificate of Completion</h1>
                <p class="subtitle">This certifies that</p>
            </div>

            <div class="content">
                <div class="recipient-name">{{ $student_name }}</div>

                <p class="certification-text">
                    has successfully completed the course
                </p>

                <div class="course-details">
                    <h2 class="course-name"> {{ $course_title }}</h2>
                </div>

                <p class="certification-text">
                    and has demonstrated satisfactory mastery of the required skills.
                </p>
            </div>

            <div class="footer">
                <table class="footer-table">
                    <tr>
                        <td class="footer-cell">
                            <h6 class="signature-name">{{ $completion_date }}</h6>
                            <div class="signature-line"></div>
                            <p class="signature-label">Date</p>
                        </td>

                        <td class="footer-cell">
                            <div class="logo-section">
                                {{-- @if($logo_base64)
                                <img src="{{ $logo_base64 }}" alt="Logo" class="logo-image" />
                                @else
                                <div class="logo-placeholder">{{ $logo_base64 }}</div>
                                @endif --}}
                            </div>
                        </td>
                        <td class="footer-cell">
                            <h6 class="signature-name">John Doe</h6>
                            <div class="signature-line"></div>
                            <p class="signature-label">Manager</p>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</body>

</html>
