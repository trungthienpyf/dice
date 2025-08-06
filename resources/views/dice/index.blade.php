<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dice Sessions</title>
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
                min-width: 200px;
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
    @include('components.userbar')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                        <h5 class="mb-0">Danh sách bảng chơi</h5>
                        <div class="d-flex flex-wrap gap-2">
                            @if ($canViewConfig)
                                <a href="{{ route('dice.configs.index') }}" class="btn btn-primary">
                                    <i class="fas fa-cog"></i> Xem cấu hình
                                </a>
                            @endif

                            @if ($canCreateDice)
                                <a href="{{ route('dice.create') }}" class="btn btn-secondary">
                                    <i class="fas fa-plus"></i> Tạo bảng
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

                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Title</th>
                                        <th>User 1</th>
                                        <th>User 2</th>
                                        <th>User 3</th>
                                        <th>User 4</th>
                                        <th class="actions-column">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($sessions as $session)
                                        <tr>
                                            <td>{{ $session->id }}</td>
                                            <td>
                                                <a href="{{ route('dice.show', $session->id) }}"
                                                    class="btn btn-sm btn-info">
                                                    Bảng ghi {{ $session->name }}
                                                </a>
                                            </td>
                                            <td>{{ $session->u1 }}</td>
                                            <td>{{ $session->u2 }}</td>
                                            <td>{{ $session->u3 }}</td>
                                            <td>{{ $session->u4 }}</td>
                                            <td class="actions-column">
                                                <div class="d-flex flex-wrap gap-1">
                                                    <a href="{{ route('dice.show', $session->id) }}"
                                                        class="btn btn-sm btn-info">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('dice.edit', $session) }}"
                                                        class="btn btn-sm btn-warning">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('dice.destroy', $session->id) }}"
                                                        method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger"
                                                            onclick="return confirm('Are you sure?')">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-center mt-4">
                            {{ $sessions->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
