<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cấu hình bảng chơi</title>
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
            -webkit-overflow-scrolling: touch;
        }

        @media (max-width: 768px) {
            .card-header {
                flex-direction: column;
                gap: 10px;
            }

            .card-header .btn {
                width: 100%;
                margin: 5px 0;
            }

            .table td,
            .table th {
                white-space: nowrap;
                min-width: 100px;
            }

            .actions-column {
                min-width: 120px;
            }
        }

        @media (max-width: 576px) {
            body {
                padding: 10px;
            }

            .container {
                padding: 0;
            }
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                        <h5 class="mb-0">Danh sách cấu hình</h5>
                        <div class="d-flex flex-wrap gap-2">
                            <a href="{{ route('dice.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Quay lại
                            </a>
                            @if ($canCreateConfig)
                                <a href="{{ route('dice.configs.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Tạo cấu hình mới
                                </a>
                            @endif

                        </div>
                    </div>

                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif

                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Id</th>
                                        <th>Tên cấu hình</th>
                                        <th>Tiền 1 chi</th>
                                        <th>Số chi để sâu</th>
                                        <th>Tên sâu</th>
                                        <th>Bộ</th>
                                        <th class="actions-column">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($configs as $config)
                                        <tr>
                                            <td>{{ $config->id }}</td>
                                            <td>{{ $config->name }}</td>
                                            <td>{{ $config->td }}</td>
                                            <td>{{ $config->cc }}</td>
                                            <td>{{ $config->ts }}</td>
                                            <td>{{ $config->same_row }}</td>
                                            <td class="actions-column">
                                                @if ($canDeleteConfig)
                                                    <form action="{{ route('dice.configs.destroy', $config) }}"
                                                        method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger"
                                                            onclick="return confirm('Bạn có chắc chắn muốn xóa cấu hình này?')">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                @endif

                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-center mt-4">
                            {{ $configs->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
