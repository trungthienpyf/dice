<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gia hạn người dùng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container py-4">
    <h3 class="mb-4">Gia hạn người dùng: <strong>{{ $user->name }}</strong></h3>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('users.extend.submit', $user->id) }}" method="POST" class="card p-4 shadow-sm">
        @csrf

        <div class="mb-3">
            <label for="days" class="form-label">Số ngày muốn gia hạn</label>
            <input type="number" class="form-control" name="days" id="days" min="1" required placeholder="Nhập số ngày muốn gia hạn">
        </div>

        <div class="mb-3">
            <label for="amount" class="form-label">Số tiền (VND)</label>
            <input type="number" class="form-control" name="amount" id="amount" required placeholder="Nhập số tiền thanh toán">
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-success">
                <i class="fas fa-check"></i> Xác nhận
            </button>
            <a href="{{ route('users.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
        </div>
    </form>
</div>

    <!-- Font Awesome + Bootstrap JS (nếu cần) -->
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</body>
</html>
