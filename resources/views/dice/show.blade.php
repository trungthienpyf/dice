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
    <script src="//cdnjs.cloudflare.com/ajax/libs/socket.io/2.4.0/socket.io.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/laravel-echo/1.11.0/echo.common.min.js"></script>
    <style>
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
            overflow-x: auto;
        }

        h1 {
            margin-top: 0;
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
        }

        /* Table full view priority - no horizontal scroll */
        .table-wrapper {
            width: 100%;
        }

        table {
            width: auto;
            min-width: 100%;
            border-collapse: collapse;
            margin-bottom: 0;
            background: white;
            /* Allow columns to expand based on content */
        }

        th, td {
            /*padding: 8px;*/
            text-align: center;
            border: 1px solid #adb5bd;
            word-wrap: break-word;
            overflow: visible !important;
            text-overflow: clip !important;
        }

        /* Add specific column widths */
        th:first-child, td:first-child {
            min-width: 60px; /* Minimum width for first column (numbers) */
        }

        th:not(:first-child):not(:last-child),
        td:not(:first-child):not(:last-child) {
            min-width: 80px; /* Minimum width for middle columns */
        }

        th:last-child, td:last-child {
            min-width: 60px; /* Minimum width for last column (total) */
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


        input .negative {
            color: #e53935 !important;
            font-weight: 500 !important;;
        }

        input .positive {
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
                border-radius: 18px;
                border-width: 4px;
            }

            h1 {
                letter-spacing: 0.5px;
                margin-bottom: 15px;
                font-size: 1.5rem;
            }

            /* Mobile table optimization */
            table {
                width: auto;
                min-width: 100%;
            }

            /* More specific selectors to override base styles */
            table th, table td {
                padding: 6px 4px;
                word-wrap: break-word;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap !important;
            }

            /* Mobile column widths */
            th:first-child, td:first-child {
                min-width: 40px; /* Minimum width for first column */
            }

            th:not(:first-child):not(:last-child),
            td:not(:first-child):not(:last-child) {
                min-width: 70px; /* Minimum width for middle columns */
            }

            th:last-child, td:last-child {
                min-width: 40px; /* Minimum width for last column */
            }

            /* Mobile input fields - more specific selector */
            table input[type="number"] {
                width: 100% !important;
                padding: 2px !important;
                min-height: 24px;
                border: none !important;
                /*background: transparent !important;*/
                text-align: center !important;
            }

            td {
                padding: 0 !important;
                min-height: 24px;
                background: transparent !important;
                text-align: center !important;
            }

            /* Mobile summary rows - more specific selector */
            .summary-row td, .highlight-row td, .tc-row td, .tt-row td {
                padding: 6px 4px;
                font-weight: bold;
            }

            .date-filter {
                flex-direction: column;
                align-items: stretch;
                gap: 8px;
                margin-bottom: 20px;
            }

            .zoom-controls {
                top: 60px;
                right: 8px;
                padding: 6px 8px;
                gap: 6px;
            }

            .zoom-btn {
                width: 28px;
                height: 28px;
                font-size: 12px;
            }

            .zoom-slider {
                width: 70px;
            }

            .zoom-level {
                font-size: 10px;
                min-width: 30px;
            }

            /* Mobile button improvements */
            .btn {
                min-height: 40px;
                font-size: 14px;
                padding: 8px 16px;
            }

            .create-table-btn {
                font-size: 12px !important;
                padding: 6px 10px !important;
                min-height: 32px !important;
            }

            .create-table-btn i {
                margin-right: 4px;
            }

            /* Mobile modal improvements */
            .modal-dialog {
                margin: 10px;
                max-width: calc(100% - 20px);
            }

            .modal-content {
                border-radius: 12px;
            }

            .modal-title {
                font-size: 18px;
            }

            .modal-body {
                font-size: 16px;
                padding: 20px;
            }
        }

        /* Extra small mobile devices */
        @media (max-width: 480px) {
            body {
                padding: 6px;
            }

            .container, .session-table {
                padding: 10px;
                margin-bottom: 12px;
                border-radius: 18px;
            }

            h1 {
                font-size: 1.3rem;
                margin-bottom: 12px;
            }

            table {
                font-size: 26px !important;
            }

            /* More specific selectors for extra small devices */
            table th, table td {
                padding: 4px 3px;
                white-space: nowrap !important;
                overflow: hidden !important;
                text-overflow: ellipsis !important;
            }

            /* More specific input selector */
            table td input[type="number"] {
                min-height: 22px;
                padding: 1px !important;
            }

            .btn {
                min-height: 36px;
                font-size: 13px;
                padding: 6px 12px;
            }

            .create-table-btn {
                font-size: 11px !important;
                padding: 4px 8px !important;
                min-height: 28px !important;
            }

            /* Ensure table fits on very small screens */
            .session-table {
                overflow-x: auto;
            }

            table {
                min-width: 100%;
            }

            /* Extra small zoom controls */
            .zoom-controls {
                top: 50px;
                right: 5px;
                padding: 4px 6px;
                gap: 4px;
            }

            .zoom-btn {
                width: 24px;
                height: 24px;
                font-size: 10px;
            }

            .zoom-slider {
                width: 50px;
            }

            .zoom-level {
                font-size: 9px;
                min-width: 25px;
            }
        }

        /* Landscape mobile orientation */
        @media (max-width: 767px) and (orientation: landscape) {
            body {
                padding: 6px;
            }

            .container, .session-table {
                padding: 10px;
                margin-bottom: 10px;
            }

            h1 {
                font-size: 1.4rem;
                margin-bottom: 10px;
            }

            /* More specific selectors for landscape */
            table th, table td {
                padding: 6px 3px;
                white-space: nowrap !important;
                overflow: hidden !important;
                text-overflow: ellipsis !important;
            }

            /* More specific input selector for landscape */
            table td input[type="number"] {
                min-height: 20px;
                padding: 2px !important;
            }

            /* Landscape zoom controls */
            .zoom-controls {
                top: 40px;
                right: 6px;
                padding: 4px 6px;
            }

            .zoom-btn {
                width: 26px;
                height: 26px;
                font-size: 11px;
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
                letter-spacing: 1px;
            }

            th, td {
                padding: 6px 4px;
            }

            input[type="number"] {
                width: 50px !important;
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


            th, td {
                padding: 7px 5px;
            }

            input[type="number"] {
                width: 55px !important;
            }
        }

        /* Desktop - optimal view */
        @media (min-width: 1200px) {
            .container, .session-table {
                max-width: 1200px;
            }

            th, td {
                padding: 8px;
            }

            input[type="number"] {
                width: 60px !important;
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

        /* Responsive styles for tt-row */
        .tt-row {
            font-size: clamp(14px, 2vw, 20px);
        }

        .value-text {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 100%;
        }

        .summary-row .summary-cell {
            background-color: #fce3d9;
            text-align: center;
            font-weight: bold;
            padding: 5px;
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

        table, table td, table th {
            font-size: 26px !important;
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

        td {
            padding: 0 !important;
        }

        .d-none {
            display: none !important;
        }
    </style>
</head>
<body>

<!-- Main content wrapper for zoom -->
<div class="zoom-wrapper" id="zoomWrapper">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
            <h1 class="mb-0 flex-grow-1">Bảng ghi {{ $session->name }}</h1>
            <a href="{{ route('dice.index') }}" class="btn btn-secondary mt-2 mt-md-0">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
        </div>
        <form class="date-filter" id="dateForm">
            {{--            <label for="date">Select date:</label>--}}
            {{--            <input type="date" id="date" name="date" value="">--}}
        </form>
    </div>
    <div id="tables-root"></div>
</div>

<!-- Password Confirmation Modal -->
<div class="modal fade" id="passwordModal" tabindex="-1" aria-labelledby="passwordModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="passwordModalLabel">Xác nhận mật khẩu</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="password">Vui lòng nhập mật khẩu để thao tác:</label>
                    <input type="password" class="form-control" id="password" placeholder="Nhập mật khẩu">
                    <div class="invalid-feedback" id="passwordError"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                <button type="button" class="btn btn-primary" id="confirmPassword">Xác nhận</button>
            </div>
        </div>
    </div>
</div>

<!-- Warning Modal -->
<div class="modal fade" id="warningModal" tabindex="-1" aria-labelledby="warningModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="warningModalLabel">Thông báo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Đã nhập sai vui lòng kiểm tra lại
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

<!-- Add this modal after the other modals, before </body> -->
<div class="modal fade" id="boModal" tabindex="-1" aria-labelledby="boModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="boModalLabel">Chọn Bộ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="boModalBody">
                <!-- Options will be injected here -->
            </div>
        </div>
    </div>
</div>

<!-- Load Bootstrap first -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Initialize modal first
    let currentInput = null;
    const passwordModal = new bootstrap.Modal(document.getElementById('passwordModal'));

    function handleInputClick(input) {
        if (input.readOnly && input.value !== '') {
            currentInput = input;
            passwordModal.show();
        }
    }

    function getMockSessions() {
        fetch(`{{ route('api.dice.get', ':id') }}`.replace(':id', idReq))
            .then(response => {
                return response.json();
            })
            .then(data => {
                console.log(data)
                renderTables(data);
            })
            .catch(error => {
                console.error('Fetch error:', error);
                alert('Lỗi: ' + error.message);
            });
    }

    const root = document.getElementById('tables-root');
    let stt = 0;
    const currentPath = window.location.pathname;
    const idReq = currentPath.split('/').pop();

    function renderTables(sessions) {
        root.innerHTML = '';
        sessions.forEach((session, index) => {
            const isLatest = index === sessions.length - 1;
            const single = renderSingleSession(session, isLatest);
            root.appendChild(single);
        });
    }

    function formatDateShort(input) {
        const [day, month, year] = input.split('/');
        const d = new Date(+year, +month - 1, +day);
        if (isNaN(d)) return 'Invalid date';

        const shortYear = d.getFullYear() % 100;
        return `${d.getDate()}/${d.getMonth() + 1}/${shortYear}`;
    }

    function renderSingleSession(session, isLatest = false) {
        stt++;
        const table = document.createElement('table');
        table.setAttribute('data-id', session.id);
        const sessionTitle = document.createElement('div');
        sessionTitle.className = 'session-title';
        const weekdayText = `Bảng ${stt} (${session.name})`;
        const highlightName = (stt - 1) % 4;
        table.innerHTML = `
        <tr class="header-row">
            <th colspan="6">${weekdayText}</th>
        </tr>
        <tr>
            <th style="width: 60px; color:red">${session.date}</th>
            <th class="${highlightName == 0 ? 'highlight-td' : ''}" >${session.u1}</th>
            <th class="${highlightName == 1 ? 'highlight-td' : ''}">${session.u2}</th>
            <th class="${highlightName == 2 ? 'highlight-td' : ''}">${session.u3}</th>
            <th class="${highlightName == 3 ? 'highlight-td' : ''}">${session.u4}</th>
            <th >Total</th>
        </tr>
        `;

        session.rows.forEach((row, i) => {
            const total = row.value.reduce((a, b) => a + b, 0);
            const type = row.type;

            const groupIndex = Math.floor(i / 3);
            const isGray = groupIndex % 2 === 1;
            const trStyle = isGray ? 'style="background: #e0e0e0;"' : '';
            table.innerHTML += `
        <tr class="data-row" ${trStyle} data-id="${row.id}">
            <td>
            <button type="button"
                class="btn btn-sm btn-warning w-100 h-100 ${row.is_show_bo === 0 || row.is_show_bo === false ? 'd-none' : ''}"
                    onclick="showBoModal('${session.u1}','${session.u2}','${session.u3}','${session.u4}', this)"
                    style="width: 100%; height: 100%; border-radius: 0; margin: 0; padding: 8px;font-size:20px;">Bộ
            </button>
            <span class="${row.is_show_bo === 1 || row.is_show_bo === true ? 'd-none' : ''}">${(i % 3) + 1}</span>

            </td>
            ${row.value.map((val, idx) => `
                <td
            >
                    <div style="display: flex; justify-content: center;">
                        <input type="${row.same_rows[idx] === null ? 'hidden' : 'number'}" readonly
                data-name="bo"
                                value="${row.same_rows[idx] === null ? '' : row.same_rows[idx]}"
                             class="${idx + 1 === row.same_cell ? 's-highlight' : ''} ${row.same_rows[idx] < 0 ? 'negative' : 'positive'}"
                            style="width: 100%; text-align: center;  border: 1px solid #ccc; border-radius: 4px;"
                            onclick="handleInputClick(this)"
                            />
                        <input type="number" ${type || val !== null ? 'readonly' : ''} value="${val ?? ''}" name="${idx}" data-index="${idx}" data-id="${row.id}"
                           class="${val < 0 ? 'negative' : 'positive'}"
                            style="width: 100%; text-align: center; background: #f9f9f9; border: 1px solid #ccc; border-radius: 4px; "
                            onclick="handleInputClick(this)" />
                    </div>
                </td>
            `).join('')}
            <td class="${-total < 0 ? 'negative' : 'positive'}">${-total}</td>
        </tr>
    `;
        });

        
        if(session.ctd){
            table.innerHTML += `
                <tr class="summary-row">
            <td class="summary-cell total">${session.ftd}</td>
            ${session.td.map(v => `
                <td class="summary-cell ${v < 0 ? 'negative' : 'positive'}">${v}</td>
            `).join('')}
            <td class="summary-cell ${session.td.reduce((a, b) => a + b, 0) < 0 ? 'negative' : 'positive'}">
                ${session.td.reduce((a, b) => a + b, 0)}
            </td>
        </tr>
            `
        }

        if(session.ctc){
            table.innerHTML += `
                <tr class="tc-row" data-id="${session.id}" style="font-size: 20px;">
        <td>TC</td>
        ${session.tc.map(v => `<td class="${v < 0 ? 'negative' : 'positive'}">${v}</td>`).join('')}
        <td class="positive">0</td>
    </tr>
            `
        }

        if(session.ccc ){
            table.innerHTML += `
                <tr class="highlight-row" data-id="${session.id}" style="font-size: 20px;">
        <td>${session.cc.reduce((a, b) => a + b, 0)}</td>
        ${session.cc.map(v => `<td class="${v < 0 ? 'negative' : 'positive'}">${v}</td>`).join('')}
        <td class="positive">  ${session.tt.reduce((a, b) => a + b, 0) + session.cc.reduce((a, b) => a + b, 0)}</td>
    </tr>
            `
        }

        if(session.ctt ){
            table.innerHTML += `
                <tr class="tt-row" data-id="${session.id}" style="font-size: 20px;">
        <td>TT</td>
        ${session.tt.map(v => `<td class="${v < 0 ? 'negative' : 'positive'}">${v.toLocaleString()}</td>`).join('')}
        <td class="align-middle">
            ${isLatest ? `
                <div class="d-flex justify-content-center">
                    <button class="btn btn-primary btn-sm create-table-btn" onclick="createNewTable(this)" title="Create New Table">
                        <i class="fas fa-plus"></i> <span class="d-none d-sm-inline">Tạo bảng</span>
                    </button>
                </div>
            ` : stt}
        </td>
    </tr>
            `
        }


        

        const wrapper = document.createElement('div');
        wrapper.className = 'session-table';
        wrapper.appendChild(sessionTitle);
        wrapper.appendChild(table); // Direct table append for full view priority

        return wrapper;
    }

    function renderForDate() {
        const sessions = getMockSessions();
    }

    function updateLockRow(row_id, c1, c2, c3, c4) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        fetch(`{{ route('api.dice.unlock', ':id') }}`.replace(':id', row_id), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                c1: c1,
                c2: c2,
                c3: c3,
                c4: c4,
            })
        }).then(response => {
            return response.json();
        })
            .catch(error => {
                console.error('Fetch error:', error);
                alert('Lỗi: ' + error.message);
            });
    }

    async function fetchDataFrom(diceId) {
        const response = await fetch(`{{ route('api.dice.fetch', ':id') }}`.replace(':id', idReq) + `?diceId=${diceId}`, {
            method: 'GET',
        });
        return response.json();
    }

    const hasTableBelow = (currentTableDiv) => {
        const next = currentTableDiv.nextElementSibling;
        return next && next.classList.contains('session-table');
    };

    function updateRow(row_id, row_next_id, values, isLock, sr) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        fetch(`{{ route('api.dice.update', ':row_id') }}`.replace(':row_id', row_id), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                row_next_id: row_next_id,
                c1: values[0] ?? null,
                c2: values[1] ?? null,
                c3: values[2] ?? null,
                c4: values[3] ?? null,
                is_lock: isLock,
                sr: sr,
            })
        }).then(response => {
            return response.json();
        })
            .then(data => {
            })
            .catch(error => {
                console.error('Fetch error:', error);
                alert('Lỗi: ' + error.message);
            });
    }

    function updateTTRow(session) {
        const ccRow = document.querySelector(`tr.highlight-row[data-id="${session.id}"]`);
        const tcRow = document.querySelector(`tr.tc-row[data-id="${session.id}"]`);
        const ttRow = document.querySelector(`tr.tt-row[data-id="${session.id}"]`);

        const ccCells = ccRow.querySelectorAll('td');

        const valueCcs = Array.from(ccCells).slice(1, -1);

        session.cc.forEach((val, idx) => {
            if (valueCcs[idx]) {
                valueCcs[idx].textContent = val.toLocaleString();
                valueCcs[idx].className = val < 0 ? 'negative' : 'positive';
            }
        });

        const tcCells = tcRow.querySelectorAll('td');

        const valueTcs = Array.from(tcCells).slice(1, -1);

        session.tc.forEach((val, idx) => {
            if (valueTcs[idx]) {
                valueTcs[idx].textContent = val.toLocaleString();
                valueTcs[idx].className = val < 0 ? 'negative' : 'positive';
            }
        });

        const ttCells = ttRow.querySelectorAll('td');

        const valueTts = Array.from(ttCells).slice(1, -1);

        session.tt.forEach((val, idx) => {
            if (valueTts[idx]) {
                valueTts[idx].textContent = val.toLocaleString();
                valueTts[idx].className = val < 0 ? 'negative' : 'positive';
            }
        });
    }

    document.addEventListener('input', function (event) {
        const input = event.target;

        if (
            input.tagName === 'INPUT' &&
            input.type === 'number' &&
            !input.readOnly &&
            input.closest('td')
        ) {
            const tr = input.closest('tr');
            const curInputs = Array.from(tr.querySelectorAll('input[type="number"]'));
            let sum = 0;

            curInputs.forEach(input => {
                const num = Number(input.value);
                if (!isNaN(num)) {
                    sum += num;
                }
            });

            const lastTd = tr.querySelector('td:last-child');
            lastTd.classList.remove('negative', 'positive');
            lastTd.classList.add(-sum < 0 ? 'negative' : 'positive');
            lastTd.textContent = -sum;
        }
    });

    document.addEventListener('change', function (event) {
        const input = event.target;

        if (
            input.tagName === 'INPUT' &&
            input.type === 'number' &&
            !input.readOnly &&
            input.closest('td')
        ) {
            console.log("on change:" + input.value.trim())
            if (isNaN(Number(input.value.trim()))) {
                return;
            }

            const tr = input.closest('tr');
            const tbody = tr.closest('tbody');
            const nextTbody = tbody.nextElementSibling;
            const curInputs = Array.from(
                tr.querySelectorAll('input[type="number"][data-index]'))
                .filter(input => input.dataset.index.trim() !== '');
            let sum = 0;
            const td = input.closest('td');
            const num = Number(input.value);
            td.classList.remove('negative', 'positive');
            td.classList.add(num < 0 ? 'negative' : 'positive');
            curInputs.forEach(input => {
                const num = Number(input.value);
                if (!isNaN(num)) {
                    sum += num;
                }
            });

            const values = curInputs.map(i => i.value);
            const idx = input.getAttribute('data-index');

            const requestValues = curInputs.map((i) => {
                return i.getAttribute('data-index') === idx ? i.value : '';
            });
            const inputsNextRow = nextTbody ? Array.from(
                    nextTbody.querySelectorAll('input[type="number"][data-index]'))
                    .filter(input => input.dataset.index.trim() !== '')
                : [];


            let rowNextId = inputsNextRow[0]?.getAttribute('data-id') || null;
            console.log(rowNextId)

            let rowId = curInputs[0].getAttribute('data-id')
            const is_lock = values.every(v => v !== '');
            if (is_lock && sum !== 0) {
                const warningModal = new bootstrap.Modal(document.getElementById('warningModal'));
                warningModal.show();
            } else {
                if (is_lock && sum === 0) {
                    inputsNextRow.forEach(i => {
                        i.readOnly = false;
                    });
                }

                updateRow(rowId, rowNextId, requestValues, is_lock, null);
                input.readOnly = true;

            }
        }
    });

    document.getElementById('confirmPassword').addEventListener('click', async function () {
        const password = document.getElementById('password').value;
        const passwordError = document.getElementById('passwordError');

        try {
            const response = await fetch('{{ route('dice.verify-password') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({password})
            });

            const data = await response.json();

            if (data.success) {
                currentInput.readOnly = false;
                const tr = currentInput.closest('tr');
                const curInputs = Array.from(
                    tr.querySelectorAll('input[type="number"][data-index]'))
                    .filter(input => input.dataset.index.trim() !== '');
                const values = curInputs.map(i => i.value);

                curInputs.forEach(i => {
                    i.readOnly = false
                    i.value = '';
                });

                let rowId = curInputs[0].getAttribute('data-id')

                const tbody = tr.closest('tbody');

                const table = tbody.closest('table');

                const tds = tr.querySelectorAll('td');
                const highlightedTd = Array.from(tds).find(td => td.classList.contains('s-highlight'));

                updateLockRow(rowId, -values[0], -values[1], -values[2], -values[3], table, table.getAttribute('data-id'), true);
                if (highlightedTd) {
                    highlightedTd.classList.remove('s-highlight');
                }
                passwordModal.hide();
                document.getElementById('password').value = '';
            } else {
                passwordError.textContent = 'Mật khẩu không đúng';
                passwordError.style.display = 'block';
            }
        } catch (error) {
            console.error('Error:', error);
            passwordError.textContent = 'Có lỗi xảy ra';
            passwordError.style.display = 'block';
        }
    });

    document.getElementById('passwordModal').addEventListener('hidden.bs.modal', function () {
        document.getElementById('passwordError').style.display = 'none';
        document.getElementById('password').value = '';
    });

    renderForDate();

    let currentZoom = 100;
    const minZoom = 25;
    const maxZoom = 300;
    const zoomStep = 10;

    function createNewTable(event) {

        fetch(`{{ route('api.new-table') }}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'

            },
            body: JSON.stringify({
                dice_id: event.closest('table').getAttribute('data-id'),
            })
        }).catch(error => {
            console.error('Fetch error:', error);
            alert('Lỗi: ' + error.message);
        });

    }

    // window.createNewTable = createNewTable;

    const echo = new Echo({
        broadcaster: 'socket.io',
        host: 'https://echo-server.treemind3.com/'
        // host: 'lottery.test:6001'
    });


    document.addEventListener('visibilitychange', function () {
        if (document.visibilityState === 'visible') {
            location.reload();
        }
    });


    echo.connector.socket.on('connect', () => {
        echo.channel(`dice.${idReq}`)
            .listen('DiceUpdated', (e) => {
                console.log('Received update:', e);
                if (e.data && e.type === 'new') {
                    const single = renderSingleSession(e.data, true);
                    root.appendChild(single);
                    const allTables = root.querySelectorAll('.session-table');
                    if (allTables.length > 1) {
                        const previousTable = allTables[allTables.length - 2];
                        const previousTTRow = previousTable.querySelector('.tt-row');
                        const lastCell = previousTTRow.querySelector('td:last-child');
                        lastCell.innerHTML = stt - 1;
                    }
                    // single.scrollIntoView({behavior: 'smooth', block: 'start'});
                    return;
                }
                if (e.data) {
                    const table = document.querySelector(`table[data-id="${e.diceTableId}"]`);

                    if (table) {
                        const tr = document.querySelector(`tr[data-id="${e.diceRowId}"]`);

                        if (e.type === 'update') {
                            handleUpdatedData(e, table, tr);
                        }
                        if (e.type === 'unlock') {
                            handleUnlockData(e, table, tr);
                        }

                    }
                }
            });
    })

    function handleUnlockData(event, table, tr) {
        const curInputs = Array.from(tr.querySelectorAll('input[type="number"]'));

        curInputs.forEach(i => {
            i.readOnly = false
            i.value = '';
            i.classList.remove('s-highlight')
            if (i.getAttribute('data-name') === 'bo') {
                i.type = 'hidden';
            }
        });
        const firstTd = tr.querySelector("td");

        if (firstTd) {
            firstTd.children[0].classList.remove('d-none');
            firstTd.children[1].classList.add('d-none');
        }
        const tds = tr.querySelectorAll('td');
        const highlightedTd = Array.from(tds).find(td => td.classList.contains('s-highlight'));

        if (highlightedTd) {
            highlightedTd.classList.remove('s-highlight');
        }

        const summaryRow = table.querySelector('.summary-row');
        const tcRow = table.querySelector('.tc-row');
        const ccRow = table.querySelector('.highlight-row');
        const ttRow = table.querySelector('.tt-row');
        const summaryCells = Array.from(summaryRow.querySelectorAll('td'));
        const tcCells = Array.from(tcRow.querySelectorAll('td'));
        const ccCells = Array.from(ccRow.querySelectorAll('td'));
        const ttCells = Array.from(ttRow.querySelectorAll('td'));

        event.data[0].td.forEach((value, index) => {
            summaryCells[index + 1].textContent = value;
            summaryCells[index + 1].classList.remove('negative', 'positive');
            summaryCells[index + 1].classList.add(value < 0 ? 'negative' : 'positive');
        });

        event.data[0].cc.forEach((value, index) => {
            ccCells[index + 1].textContent = value;
            ccCells[index + 1].classList.remove('negative', 'positive');
            ccCells[index + 1].classList.add(value < 0 ? 'negative' : 'positive');
        });

        event.data[0].tt.forEach((value, index) => {
            ttCells[index + 1].textContent = value.toLocaleString();
            ttCells[index + 1].classList.remove('negative', 'positive');
            ttCells[index + 1].classList.add(value < 0 ? 'negative' : 'positive');
        });

        event.data[0].tc.forEach((value, index) => {
            tcCells[index + 1].textContent = value;
            tcCells[index + 1].classList.remove('negative', 'positive');
            tcCells[index + 1].classList.add(value < 0 ? 'negative' : 'positive');
        });
        ccCells[0].textContent = event.data[0].cc.reduce((a, b) => a + b, 0);
        fetchDataFrom(event.diceTableId).then(
            item => {
                item.forEach(session => {
                    updateTTRow(session);
                });
            }
        );
    }


    function handleUpdatedData(event, table, tr) {
        const firstTd = tr.querySelector("td");

        if (event.sri) {
            const inputCells = tr.querySelectorAll('input[type="hidden"]');

            if (inputCells[event.sri - 1]) {
                inputCells[event.sri - 1].classList.add('s-highlight');

            }

            inputCells.forEach((val, idx) => {
                val.value = event.srs[idx];
                val.type = 'number';

                val.classList.remove('negative', 'positive');
                val.classList.add(val.value < 0 ? 'negative' : 'positive');
            })

            firstTd.children[0].classList.add('d-none');
            firstTd.children[1].classList.remove('d-none');
        }


        const tbody = tr.closest('tbody');
        const nextTbody = tbody.nextElementSibling;
        const curInputs = Array.from(
            tr.querySelectorAll('input[type="number"][data-index]'))
            .filter(input => input.dataset.index.trim() !== '');
        let sum = 0;

        for (let i = 0; i < 4; i++) {
            if (document.activeElement !== curInputs[i]) {

                if (event.rows[i] !== null) {
                    curInputs[i].value = event.rows[i];
                }

                if (curInputs[i].value !== '' && curInputs[i].value !== null) {
                    if (event.data[0].is_sr_act === false) {
                        curInputs[i].readOnly = true;
                    }
                    const num = Number(curInputs[i].value);
                    if (!isNaN(num)) {
                        sum += num;

                        const td = curInputs[i].closest('td');
                        td.classList.remove('negative', 'positive');
                        td.classList.add(num < 0 ? 'negative' : 'positive');
                    }
                }
            }
        }


        const lastTd = tr.querySelector('td:last-child');
        lastTd.classList.remove('negative', 'positive');
        lastTd.classList.add(-(sum) < 0 ? 'negative' : 'positive');
        lastTd.textContent = -(sum);

        const inputsNextRow = nextTbody ? Array.from(
            nextTbody.querySelectorAll('input[type="number"][data-index]'))
            .filter(input => input.dataset.index.trim() !== '') : [];

        const is_lock = event.rows.every(v => v !== '' && v !== null);

        const firstTdNext = nextTbody.querySelector("td");

        if ((firstTd && is_lock)) {
            firstTd.children[0].classList.add('d-none');
            firstTd.children[1].classList.remove('d-none');
            if (firstTdNext && event.data[0].is_unlock_next_row === true) {
                firstTdNext.children[0].classList.remove('d-none');
                firstTdNext.children[1].classList.add('d-none');
            }
        }


        if (is_lock && event.data[0].is_unlock_next_row === true) {
            inputsNextRow.forEach(i => i.readOnly = false);
        }


        const summaryRow = table.querySelector('.summary-row');
        const tcRow = table.querySelector('.tc-row');
        const ccRow = table.querySelector('.highlight-row');
        const ttRow = table.querySelector('.tt-row');
        const summaryCells = Array.from(summaryRow.querySelectorAll('td'));
        const tcCells = Array.from(tcRow.querySelectorAll('td'));
        const ccCells = Array.from(ccRow.querySelectorAll('td'));
        const ttCells = Array.from(ttRow.querySelectorAll('td'));

        event.data[0].td.forEach((value, index) => {
            summaryCells[index + 1].textContent = value;
            summaryCells[index + 1].classList.remove('negative', 'positive');
            summaryCells[index + 1].classList.add(value < 0 ? 'negative' : 'positive');
        });

        event.data[0].cc.forEach((value, index) => {
            ccCells[index + 1].textContent = value;
            ccCells[index + 1].classList.remove('negative', 'positive');
            ccCells[index + 1].classList.add(value < 0 ? 'negative' : 'positive');
        });

        event.data[0].tt.forEach((value, index) => {
            ttCells[index + 1].textContent = value.toLocaleString();
            ttCells[index + 1].classList.remove('negative', 'positive');
            ttCells[index + 1].classList.add(value < 0 ? 'negative' : 'positive');
        });

        event.data[0].tc.forEach((value, index) => {
            tcCells[index + 1].textContent = value;
            tcCells[index + 1].classList.remove('negative', 'positive');
            tcCells[index + 1].classList.add(value < 0 ? 'negative' : 'positive');
        });

        ccCells[0].textContent = event.data[0].cc.reduce((a, b) => a + b, 0);


        if (event.data[0].is_unlock_next_row === false
            || hasTableBelow(table.closest('.session-table'))) {
            fetchDataFrom(table.getAttribute('data-id')).then(
                item => {
                    item.forEach(session => {
                        updateTTRow(session);
                    });
                }
            );
        }
    }

    let boModalInstance = null;
    let lastBoBtn = null;

    function showBoModal(u1, u2, u3, u4, btn) {
        lastBoBtn = btn;
        const boModal = document.getElementById('boModal');
        const boModalBody = document.getElementById('boModalBody');
        boModalBody.innerHTML = '';
        [u1, u2, u3, u4].forEach((name, idx) => {
            const optionBtn = document.createElement('button');
            optionBtn.className = 'btn btn-outline-primary w-100 mb-2';
            optionBtn.textContent = name;
            optionBtn.onclick = function () {
                console.log('Selected Bộ Index:', idx);
                console.log('Selected Bộ:', name);
                if (lastBoBtn) {
                    const tr = lastBoBtn.closest('tr');

                    if (tr) {

                        const tbody = tr.closest('tbody');
                        const nextTbody = tbody.nextElementSibling;
                        const curInputs = Array.from(
                            tr.querySelectorAll('input[type="number"][data-index]'))
                            .filter(input => input.dataset.index.trim() !== '');
                        let sum = 0;
                        curInputs.forEach(input => {
                            const num = Number(input.value);
                            if (!isNaN(num)) {
                                sum += num;
                            }
                        });

                        const inputsNextRow = nextTbody ? Array.from(
                            nextTbody.querySelectorAll('input[type="number"][data-index]'))
                            .filter(input => input.dataset.index.trim() !== '') : [];

                        let rowNextId = inputsNextRow[0]?.getAttribute('data-id') || null;

                        let rowId = curInputs[0].getAttribute('data-id')

                        updateRow(rowId, rowNextId, [null, null, null, null], false, idx + 1);

                    }
                }
                if (boModalInstance) boModalInstance.hide();
            };
            boModalBody.appendChild(optionBtn);
        });
        if (!boModalInstance) {
            boModalInstance = new bootstrap.Modal(boModal);
        }
        boModalInstance.show();
    }

</script>
</body>
</html>