<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Thống kê thuê của {{ $user->name }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS (CDN) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <div class="container py-4">
        <h2 class="mb-4">Thống kê gia hạn của: <strong>{{ $user->name }}</strong></h2>

        <div class="card shadow-sm">
            <div class="card-body">
                @if ($rents->isEmpty())
                    <p class="text-muted">Người dùng này chưa từng được gia hạn.</p>
                @else
                    <table class="table table-bordered align-middle text-center">
                        <thead class="table-secondary">
                            <tr>
                                <th>Ngày bắt đầu</th>
                                <th>Ngày kết thúc</th>
                                <th>Số ngày</th>
                                <th>Số tiền (VNĐ)</th>
                                <th>Ngày gia hạn</th>                                
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($rents as $rent)
                                @php
                                    $isActive = \Carbon\Carbon::now()->between($rent->start_date, $rent->end_date);
                                    $days = \Carbon\Carbon::parse($rent->start_date)->diffInDays($rent->end_date);
                                @endphp
                                <tr class="{{ $isActive ? 'fw-bold table-success' : '' }}">
                                    <td>{{ \Carbon\Carbon::parse($rent->start_date)->format('d/m/Y h:m:s') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($rent->end_date)->format('d/m/Y h:m:s') }}</td>
                                    <td>{{ $days }} ngày</td>
                                    <td class="text-end text-success">{{ number_format($rent->amount, 0, ',', '.') }}đ
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($rent->created_at)->format('d/m/Y H:i') }}</td>
                                </tr>
                            @endforeach 
                        </tbody>

                    </table>

                    <div class="mt-3">
                        <h5>Tổng tiền: <span
                                class="text-primary fw-bold">{{ number_format($total, 0, ',', '.') }}đ</span></h5>
                    </div>
                @endif

                <div class="mt-4">
                    <a href="{{ route('users.index') }}" class="btn btn-secondary">← Quay lại danh sách</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS (optional) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
