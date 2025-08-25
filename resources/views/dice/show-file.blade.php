<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests"/>
    <title>Random Dice Table</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        /* Zoom wrapper - this will contain everything that needs to be zoomed */
        .zoom-wrapper {
            transform-origin: top left;
            transition: transform 0.2s ease;
            width: 100%;
        }

        body {
            background: linear-gradient(135deg, #fff700 0%, #ffe259 100%);
            font-family: 'Roboto', Arial, sans-serif;
            margin: 0;
            min-height: 100vh;
            padding: 20px;
            overflow-x: auto;
        }

        .container {
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12);
            border: 4px solid #222;
            padding: 32px 24px 24px 24px;
            max-width: 1200px;
            margin: 0 auto 30px auto;
            width: 100%;
        }

        .session-table {
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12);
            border: 4px solid #222;
            padding: 24px;
            /*max-width: 1200px;*/
            margin: 0 auto 30px auto;
            width: 100%;
        }

        h1 {
            margin-top: 0;
            font-size: 2.2em;
            color: #333;
            letter-spacing: 2px;
        }

        .date-filter {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 24px;
            flex-wrap: wrap;
        }

        .date-filter label {
            font-weight: 500;
            color: #222;
        }

        .date-filter input[type="date"] {
            padding: 6px 12px;
            border-radius: 6px;
            border: 1px solid #bbb;
            font-size: 1em;
        }

        /* Table full view priority - no horizontal scroll */
        .table-wrapper {
            width: 100%;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 0;
            background: white;
            /* table-layout: fixed; */
        }

        th, td {
            padding: 8px;
            text-align: center;
            border: 1px solid #adb5bd;
            word-wrap: break-word;
            overflow: hidden;
        }

        /* Add specific column widths */
        th:first-child, td:first-child {
            width: 60px; /* First column (numbers) */
        }

        th:not(:first-child):not(:last-child),
        td:not(:first-child):not(:last-child) {
            width: calc((100% - 120px) / 4); /* Divide remaining space by 4 for middle columns */
        }

        th:last-child, td:last-child {
            width: 60px; /* Last column (total) */
        }

        /* Ensure input fields respect column width */
        td input[type="number"] {
            width: 100% !important;
            max-width: 100%;
            box-sizing: border-box;
        }

        th {
            background-color: #f8f9fa;
            font-weight: bold;
        }

        .header-row th {
            background-color: #e9ecef;
            border-bottom: 2px solid #adb5bd;
        }

        .data-row td {
            border-bottom: 1px solid #adb5bd;
        }

        .summary-row td, .highlight-row td, .tc-row td, .tt-row td {
            border-bottom: 1px solid #adb5bd;
            font-weight: bold;
        }

        .highlight-row {
            background-color: #fff3cd;
        }

        .tc-row {
            background-color: #d1e7dd;
        }

        .tt-row {
            background-color: #cfe2ff;
        }

        .negative {
            color: #e53935;
            font-weight: 500;
        }

        .positive {
            color: rgb(0, 0, 0);
            font-weight: 500;
        }

        /* Excel-style Zoom Controls - positioned to not cover table content */
        .zoom-controls {
            position: fixed;
            top: 80px;
            right: 20px;
            z-index: 1000;
            display: flex;
            align-items: center;
            gap: 10px;
            background: rgba(255, 255, 255, 0.95);
            padding: 8px 12px;
            border-radius: 25px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            border: 1px solid #ddd;
        }

        .zoom-btn {
            width: 32px;
            height: 32px;
            border: 1px solid #ccc;
            border-radius: 4px;
            background: #fff;
            color: #333;
            font-size: 14px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
        }

        .zoom-btn:hover {
            background: #f0f0f0;
            border-color: #999;
        }

        .zoom-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .zoom-slider {
            width: 100px;
            height: 4px;
            background: #ddd;
            border-radius: 2px;
            outline: none;
            cursor: pointer;
        }

        .zoom-level {
            font-size: 12px;
            color: #666;
            min-width: 40px;
            text-align: center;
        }

        /* Responsive Design - Full Table View Priority */

        /* Mobile devices - prioritize full table view */
        @media (max-width: 767px) {
            body {
                padding: 8px;
            }

            .container, .session-table {
                padding: 12px;
                margin-bottom: 15px;
                border-radius: 10px;
                border-width: 2px;
            }

            h1 {
                font-size: 1.3em;
                letter-spacing: 0.5px;
            }

            /* Compress table to fit full view */
            th, td {
                padding: 4px 2px;
                font-size: 1rem;
            }

            /* Make input fields smaller to fit */
            input[type="number"] {
                width: 40px !important;
                font-size: 1rem !important;
                padding: 2px !important;
            }

            .date-filter {
                flex-direction: column;
                align-items: stretch;
                gap: 6px;
            }

            .zoom-controls {
                top: 60px;
                right: 10px;
                padding: 6px 8px;
                gap: 6px;
            }

            .zoom-btn {
                width: 26px;
                height: 26px;
                font-size: 11px;
            }

            .zoom-slider {
                width: 70px;
            }

            .zoom-level {
                font-size: 10px;
                min-width: 30px;
            }
        }

        /* Small tablets - balance between full view and readability */
        @media (min-width: 768px) and (max-width: 991px) {
            body {
                padding: 12px;
            }

            .container, .session-table {
                padding: 18px;
                border-radius: 12px;
                border-width: 3px;
            }

            h1 {
                font-size: 1.6em;
                letter-spacing: 1px;
            }

            th, td {
                padding: 6px 4px;
                font-size: 0.85em;
            }

            input[type="number"] {
                width: 50px !important;
                font-size: 1rem !important;
            }
        }

        /* Large tablets and small desktops */
        @media (min-width: 992px) and (max-width: 1199px) {
            body {
                padding: 15px;
            }

            .container, .session-table {
                padding: 20px;
                border-radius: 15px;
            }

            h1 {
                font-size: 1.8em;
            }

            th, td {
                padding: 7px 5px;
                font-size: 0.9em;
            }

            input[type="number"] {
                width: 55px !important;
                font-size: 1rem !important;
            }
        }

        /* Desktop - optimal view */
        @media (min-width: 1200px) {
            .container, .session-table {
                max-width: 1200px;
            }

            th, td {
                padding: 8px;
                font-size: 1em;
            }

            input[type="number"] {
                width: 60px !important;
                font-size: 1rem !important;
            }
        }

        input[readonly]:focus {
            outline: none;
            box-shadow: none;
            border: none;
        }

        .highlight-td {
            /*background-color: #d4f1ff !important;*/
            background-color: #f4de5a8a;
        }

        .s-highlight {
            background-color: #e781ff !important;
        }

        td .s-highlight {
            background-color: #e781ff !important;
        }

        input .s-highlight {
            background-color: #e781ff !important;
        }


        input[type=number]::-webkit-inner-spin-button,
        input[type=number]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        /* Touch-friendly improvements */
        input[type="number"] {
            touch-action: manipulation;
            -webkit-tap-highlight-color: transparent;
            width: 100% !important;
            border: none !important;
            outline: none !important;
            padding: 0 !important;
            margin: 0 !important;
            background: transparent !important;
            text-align: center !important;
        }

        td {
            /*padding: 0!important;*/
            /*margin: 8px!important;*/
        }

        /* Better button styling for touch */
        .btn {
            min-height: 44px;
            touch-action: manipulation;
        }

        /* Create Table Button */
        .create-table-btn {
            font-size: 0.8em !important;
            padding: 4px 8px !important;
            border-radius: 4px !important;
            white-space: nowrap;
            min-height: auto !important;
        }

        .create-table-btn i {
            margin-right: 4px;
        }

        /* Responsive button sizing */
        @media (max-width: 767px) {
            .create-table-btn {
                font-size: 1rem !important;
                padding: 3px 6px !important;
            }

            .create-table-btn i {
                margin-right: 2px;
            }
        }

        /* Improve modal for mobile */
        @media (max-width: 767px) {
            .modal-dialog {
                margin: 10px;
                max-width: calc(100% - 20px);
            }

            .modal-content {
                border-radius: 8px;
            }
        }

        Add these styles to your existing styles
        .tt-row {
            font-size: clamp(14px, 2vw, 20px);
        }

        .value-text {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 100%;
        }

        @media (max-width: 768px) {
            .tt-row td {
                padding: 0.5rem;
            }

            .create-table-btn {
                padding: 0.25rem 0.5rem;
            }
        }

        @media (max-width: 576px) {
            .tt-row td {
                padding: 0.25rem;
            }

            .value-text {
                font-size: 0.9rem;
            }
        }

        .summary-row .summary-cell {
            background-color: #fce3d9;

            text-align: center;
            font-weight: bold;
            padding: 5px;
            /*font-size: 20px;*/
        }

        .summary-cell.negative {
            color: red;
        }

        .summary-cell.positive {
            color: black;
        }

        .summary-cell.total {
            position: relative;
        }

    </style>
</head>
<body>


<!-- Main content wrapper for zoom -->
<div class="zoom-wrapper" id="zoomWrapper">
    {{--    <div class="container">--}}
    {{--        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">--}}
    {{--            <h1 class="mb-0 flex-grow-1">Bảng ghi {{ $session->name }}</h1>--}}
    {{--            <a href="{{ route('dice.index') }}" class="btn btn-secondary mt-2 mt-md-0">--}}
    {{--                <i class="fas fa-arrow-left"></i> Quay lại--}}
    {{--            </a>--}}
    {{--        </div>--}}
    {{--        <form class="date-filter" id="dateForm">--}}
    {{--            --}}{{--            <label for="date">Select date:</label>--}}
    {{--            --}}{{--            <input type="date" id="date" name="date" value="">--}}
    {{--        </form>--}}
    {{--    </div>--}}
    <div id="tables-root">
        @php $stt = 0; @endphp
        <div id="root">
            @foreach ($sessions as $index => $session)
                @php
                    $stt++;
                    $isLatest = $index === count($sessions) - 1;
                    $highlightName = ($stt - 1) % 4;

                    $totalTd = array_sum($session['td']);
                    $totalTt = array_sum($session['tt']);
                    $totalCc = array_sum($session['cc']);
                    $totalTtCc = $totalTt + $totalCc;
                @endphp

                <div class="session-table">
                    <div class="session-title">
                        Bảng {{ e($stt) }} ({{ e($session['name']) }})
                    </div>
                    <table data-id="{{ e($session['id']) }}">
                        <tr class="header-row">
                            <th colspan="6">Bảng {{ e($stt) }} ({{ e($session['name']) }})</th>
                        </tr>
                        <tr>
                            <th style="width: 60px; color:red">{{ \Carbon\Carbon::createFromFormat('d/m/Y', $session['date'])->format('d/m/y') }}</th>
                            <th class="{{ $highlightName == 0 ? 'highlight-td' : '' }}">{{ e($session['u1']) }}</th>
                            <th class="{{ $highlightName == 1 ? 'highlight-td' : '' }}">{{ e($session['u2']) }}</th>
                            <th class="{{ $highlightName == 2 ? 'highlight-td' : '' }}">{{ e($session['u3']) }}</th>
                            <th class="{{ $highlightName == 3 ? 'highlight-td' : '' }}">{{ e($session['u4']) }}</th>
                            <th>Total</th>
                        </tr>

                        @foreach ($session['rows'] as $i => $row)
                            @php
                                $groupIndex = floor($i / 3);
                                $isGray = $groupIndex % 2 === 1;
                                $total = array_sum($row['value']);
                            @endphp
                            <tr class="data-row" @if($isGray) style="background: #e0e0e0;" @endif>
                                <td>{{ ($i % 3) + 1 }}</td>
                                @foreach ($row['value'] as $idx => $val)
                                    @php
                                        $classes = $val < 0 ? 'negative' : 'positive';
                                        if (($idx + 1) == $row['same_cell']) {
                                            $classes .= ' s-highlight';
                                        }
                                    @endphp


                                    {{--                                        <input type="number" name="{{ $idx }}"--}}
                                    {{--                                               data-index="{{ $idx }}" data-id="{{ $row['id'] }}"--}}
                                    {{--                                               value="{{ $val ?? '' }}"--}}
                                    {{--                                               style="width: 60px; text-align: center; background: #f9f9f9; border: 1px solid #ccc; border-radius: 4px; color: inherit; font-weight: inherit;"--}}
                                    {{--                                               onclick="handleInputClick(this)" readonly/>--}}
                                    {{--                                    </td>--}}

                                    <td>

                                        <div style="display: flex; justify-content: center;">
                                            <input type="{{ $row['same_rows'][$idx] == null ? 'hidden' : 'number' }}"
                                                   readonly
                                                   data-name="bo"
                                                   value="{{ $row['same_rows'][$idx] === null ? '' : e($row['same_rows'][$idx]) }}"
                                                   class="{{ ($idx + 1) === $row['same_cell'] ? 's-highlight' : '' }} {{ ($row['same_rows'][$idx] ?? 0) < 0 ? 'negative' : 'positive' }}"
                                                   style="{{ ($idx + 1) === $row['same_cell'] ? 'background-color: #e781ff !important;' : '' }} width: 100%; text-align: center; border: 1px solid #ccc; border-radius: 4px;"
                                                   onclick="handleInputClick(this)"
                                            />
                                            <input type="number"
                                                   readonly
                                                   value="{{ e($val ?? '') }}"
                                                   name="{{ e($idx) }}"
                                                   data-index="{{ e($idx) }}"
                                                   data-id="{{ e($row['id']) }}"
                                                   class="{{ ($val ?? 0) < 0 ? 'negative' : 'positive' }}"
                                                   style="width: 60px; text-align: center; background: #f9f9f9; border: 1px solid #ccc; border-radius: 4px; font-weight: inherit;"
                                                   onclick="handleInputClick(this)"/>
                                        </div>
                                    </td>

                                @endforeach
                                <td class="{{ $total < 0 ? 'negative' : 'positive' }}">{{ e($total) }}</td>
                            </tr>
                        @endforeach

                        <tr class="summary-row">
                            <td class="summary-cell total">{{ e($session['ftd']) }}</td>
                            @foreach ($session['td'] as $v)
                                <td class="summary-cell {{ $v < 0 ? 'negative' : 'positive' }}">{{ e($v) }}</td>
                            @endforeach
                            <td class="summary-cell {{ $totalTd < 0 ? 'negative' : 'positive' }}">{{ e($totalTd) }}</td>
                        </tr>

                        <tr class="tc-row" data-id="{{ e($session['id']) }}" style="font-size: 20px;">
                            <td>TC</td>
                            @foreach ($session['tc'] as $v)
                                <td class="{{ $v < 0 ? 'negative' : 'positive' }}">{{ e($v) }}</td>
                            @endforeach
                            <td class="positive">0</td>
                        </tr>

                        <tr class="highlight-row" data-id="{{ e($session['id']) }}" style="font-size: 20px;">
                            <td>{{ e($totalCc) }}</td>
                            @foreach ($session['cc'] as $v)
                                <td class="{{ $v < 0 ? 'negative' : 'positive' }}">{{ e($v) }}</td>
                            @endforeach
                            <td class="positive">{{ e($totalTtCc) }}</td>
                        </tr>


                        <tr class="tt-row" data-id="{{ e($session['id']) }}" style="font-size: 20px;">
                            <td>TT</td>
                            @foreach ($session['tt'] as $v)
                                <td class="{{ $v < 0 ? 'negative' : 'positive' }}">{{ number_format($v) }}</td>
                            @endforeach
                            <td class="align-middle">
                                @if ($isLatest)

                                @else
                                    {{ e($stt) }}
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            @endforeach
        </div>

    </div>
</div>


</body>
</html>
