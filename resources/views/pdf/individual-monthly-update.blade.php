<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Individual Monthly Updates - {{ $customer->lastname }}, {{ $customer->firstname }} ({{ $customer->phone }}) - {{ $period }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 10pt;
            line-height: 1.3;
            color: #000;
        }

        .page {
            width: 100%;
            /* padding: 100px !important; */
            /* padding-left: 20px !important;
            padding-right: 500px !important; */
            padding-top: 20px !important;
            padding-bottom: 20px !important;
            max-width: 100%;
            overflow: hidden;
        }

        .main-title {
            text-align: center;
            font-size: 16pt;
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 20px;
            padding-left: 20px !important;
            padding-right: 20px !important;
        }

        .header-info {
            display: table;
            width: 100%;
            margin-bottom: 15px;
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
            /* padding-right: 20px !important; */
        }

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
            font-size: 9pt;
            word-wrap: break-word;
            overflow-wrap: break-word;
            hyphens: auto;
        }
        .main-table th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: left;
            width: 30%;
        }

        .main-table td {
            width: 70%;
        }

        .checkbox-row {
            margin-bottom: 10px;
        }

        .checkbox {
            display: inline-block;
            width: 12px;
            height: 12px;
            border: 1px solid #000;
            margin-right: 5px;
            vertical-align: middle;
        }

        .checkbox.checked {
            background-color: #000;
        }

        .signature-section {
            margin-top: 30px;
            padding-left: 20px !important;
        }

        .signature-line {
            margin-bottom: 8px;
        }
    </style>
</head>

<body>
    <div class="page">
        <div class="main-title">Individual Monthly Updates</div>

        <div class="header-info">
            <div class="header-left">
                <strong>Name:</strong> {{ $customer->lastname }}, {{ $customer->firstname }} ({{ $customer->phone }})
            </div>
            <div class="header-right">
                <strong>Month of:</strong> {{ $period }}
            </div>
        </div>

        <table class="main-table">
            <tr>
                <th>Overall Health Information</th>
                <td>{{ $update->overall_health_information ?? '' }}</td>
            </tr>
            <tr>
                <th>Appointments</th>
                <td>{{ $update->appointments ?? '' }}</td>
            </tr>
            <tr>
                <th>Social Activities (dates and activities)</th>
                <td>{{ $update->social_activities ?? '' }}</td>
            </tr>
            <tr>
                <th>Is there any change in condition or medication? (If yes, please describe and put the date it occurs)
                </th>
                <td>
                    <div class="checkbox-row">
                        YES <span class="checkbox {{ $update->condition_medication_change ? 'checked' : '' }}"></span>
                        &nbsp;&nbsp;
                        NO <span class="checkbox {{ !$update->condition_medication_change ? 'checked' : '' }}"></span>
                    </div>
                    @if($update->condition_medication_change && $update->condition_medication_change_description)
                    {{ $update->condition_medication_change_description }}
                    @endif
                </td>
            </tr>
            <tr>
                <th>Is there any new behavior? (If yes, please describe and put the date it occurs)</th>
                <td>
                    <div class="checkbox-row">
                        YES <span class="checkbox {{ $update->new_behavior ? 'checked' : '' }}"></span>
                        &nbsp;&nbsp;
                        NO <span class="checkbox {{ !$update->new_behavior ? 'checked' : '' }}"></span>
                    </div>
                    @if($update->new_behavior && $update->new_behavior_description)
                    {{ $update->new_behavior_description }}
                    @endif
                </td>
            </tr>
            <tr>
                <th>Is there anything specific to report about this individual? (If yes, please describe and put the
                    date you noticed)</th>
                <td>
                    <div class="checkbox-row">
                        YES <span class="checkbox {{ $update->specific_report ? 'checked' : '' }}"></span>
                        &nbsp;&nbsp;
                        NO <span class="checkbox {{ !$update->specific_report ? 'checked' : '' }}"></span>
                    </div>
                    @if($update->specific_report && $update->specific_report_description)
                    {{ $update->specific_report_description }}
                    @endif
                </td>
            </tr>
        </table>

        <div class="signature-section">
            <div class="signature-line">
                <strong>Staff name:</strong>____{{ $update->createdBy?->firstname }} {{ $update->createdBy?->lastname
                }}___________
            </div>
        </div>
    </div>
</body>

</html>