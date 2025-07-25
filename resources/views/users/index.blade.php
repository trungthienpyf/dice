<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Quản lý người dùng</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap & FontAwesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f8f9fa;
            padding: 15px;
        }

        .card {
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .table th {
            background-color: #f8f9fa;
        }

        .btn-sm {
            margin: 2px;
        }

        .table-responsive {
            overflow-x: auto;
        }

        .actions-column {
            min-width: 160px;
        }

        @media (max-width: 576px) {
            .actions-column {
                min-width: 100px;
            }

            .form-inline .form-control {
                width: 100% !important;
            }

            .card-header h5 {
                font-size: 1rem;
            }
        }
    </style>
</head>

<body>
    <!-- Nếu có userbar thì include -->
    @include('components.userbar')

    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-center">
                        <h5 class="mb-2 mb-md-0">Danh sách người dùng</h5>
                        <div class="d-flex flex-wrap gap-2">
                            <a href="{{ route('users.create') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-user-plus"></i> Tạo user
                            </a>
                        </div>
                    </div>

                    <div class="card-body">
                        <form method="GET" action="{{ route('users.index') }}"
                            class="mb-3 d-flex flex-wrap align-items-center gap-2">
                            <input type="text" name="search" class="form-control form-control-sm"
                                style="max-width: 200px;" placeholder="Tìm..." value="{{ request('search') }}">
                            <button type="submit" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-search"></i>
                            </button>
                        </form>

                        @if (session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

                        <div id="alert-box"></div>

                        <div class="table-responsive">
                            <table class="table table-striped table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Tên</th>
                                        <th>Username</th>
                                        <th>Quyền</th>
                                        <th>Giá thuê</th>
                                        <th>Hết hạn</th>
                                        <th class="actions-column text-center">Hành động</th>
                                    </tr>
                                </thead>
                                <tbody id="userTableBody">
                                    @foreach ($sessions as $session)
                                        <tr>
                                            <td>{{ $session->id }}</td>
                                            <td>{{ $session->name }}</td>
                                            <td>{{ $session->username }}</td>
                                            <td>{{ $session->roles->first()->name ?? 'Không có' }}</td>
                                            <td>{{ round($session->amount) }}</td>
                                            <td ondblclick="goToStats({{ $session->id }})">
                                                @php
                                                    $seconds = $session->status;
                                                    if (is_null($seconds)) {
                                                        $label =
                                                            '<span class="badge bg-secondary">Chưa kích hoạt</span>';
                                                    } elseif ($seconds > 86400) {
                                                        $days = floor($seconds / 86400);
                                                        $label =
                                                            '<span class="badge bg-success">Còn ' .
                                                            $days .
                                                            ' ngày</span>';
                                                    } elseif ($seconds > 3600) {
                                                        $hours = floor($seconds / 3600);
                                                        $label =
                                                            '<span class="badge bg-success">Còn ' .
                                                            $hours .
                                                            ' giờ</span>';
                                                    } elseif ($seconds > 60) {
                                                        $minutes = floor($seconds / 60);
                                                        $label =
                                                            '<span class="badge bg-success">Còn ' .
                                                            $minutes .
                                                            ' phút</span>';
                                                    } elseif ($seconds > 0) {
                                                        $label =
                                                            '<span class="badge bg-success">Còn ' .
                                                            $seconds .
                                                            ' giây</span>';
                                                    } elseif ($seconds > -60) {
                                                        $label =
                                                            '<span class="badge bg-danger">Hết hạn ' .
                                                            abs($seconds) .
                                                            ' giây</span>';
                                                    } elseif ($seconds > -3600) {
                                                        $minutes = floor(abs($seconds) / 60);
                                                        $label =
                                                            '<span class="badge bg-danger">Hết hạn ' .
                                                            $minutes .
                                                            ' phút</span>';
                                                    } elseif ($seconds > -86400) {
                                                        $hours = floor(abs($seconds) / 3600);
                                                        $label =
                                                            '<span class="badge bg-danger">Hết hạn ' .
                                                            $hours .
                                                            ' giờ</span>';
                                                    } else {
                                                        $days = floor(abs($seconds) / 86400);
                                                        $label =
                                                            '<span class="badge bg-danger">Hết hạn ' .
                                                            $days .
                                                            ' ngày</span>';
                                                    }
                                                @endphp
                                                {!! $label !!}
                                            </td>
                                            <td>
                                                <div class="d-flex flex-wrap justify-content-center gap-1">
                                                    <a href="{{ route('users.edit', $session) }}"
                                                        class="btn btn-sm btn-warning" title="Chỉnh sửa">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('users.destroy', $session->id) }}"
                                                        method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger"
                                                            onclick="return confirm('Are you sure?')" title="Xoá">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>

                                                    @if ($extend_permission)
                                                        <a href="{{ route('users.extend', $session->id) }}"
                                                            class="btn btn-sm btn-primary" title="Gia hạn">
                                                            <i class="fas fa-calendar-plus"></i>
                                                        </a>
                                                    @endif

                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            <div class="mt-3">
                                {{ $sessions->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function goToStats(userId) {
            window.location.href = `/users/${userId}/rents/stats`;
        }
    </script>
</body>

</html>
