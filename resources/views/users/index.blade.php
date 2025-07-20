<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Quản lý người dùng</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
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
    </style>
</head>

<body>
    @include('components.userbar')

    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                        <h5 class="mb-0">Danh sách người dùng</h5>
                        <div class="d-flex flex-wrap gap-2">
                            <a href="{{ route('users.create') }}" class="btn btn-secondary">
                                <i class="fas fa-user-plus"></i> Tạo user
                            </a>
                            {{-- <button class="btn btn-primary" onclick="showCreateModal()">
                            <i class="fas fa-user-plus"></i> Tạo user
                        </button> --}}
                        </div>
                    </div>
                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif
                        <div id="alert-box"></div>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Tên</th>
                                        <th>Username</th>
                                        <th>Quyền</th>
                                        <th>Ngày hết hạn</th>
                                        <th class="actions-column">Hành động</th>
                                    </tr>
                                </thead>
                                <tbody id="userTableBody">
                                    @foreach ($sessions as $session)
                                        <tr>
                                            <td>{{ $session->id }}</td>
                                            <td>{{ $session->username }}</td>
                                            <td>{{ $session->name }}</td>
                                            <td>{{ $session->roles->first()->name ?? 'Không có' }}</td>
                                            <td>{{ $session->expired_at}}</td>
                                            <td class="actions-column">
                                                <div class="d-flex flex-wrap gap-1">
                                                    {{-- <a href="{{ route('dice.show', $session->id) }}"
                                                        class="btn btn-sm btn-info">
                                                        <i class="fas fa-eye"></i>
                                                    </a> --}}
                                                    <a href="{{ route('users.edit', $session) }}"
                                                        class="btn btn-sm btn-warning">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('users.destroy', $session->id) }}"
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
                            <div class="mt-3">
                                {{ $sessions->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const token = localStorage.getItem('access_token');
        const userTable = document.getElementById('userTableBody');
        const alertBox = document.getElementById('alert-box');

        function showAlert(message, type = 'success') {
            alertBox.innerHTML = `<div class="alert alert-${type}">${message}</div>`;
            setTimeout(() => alertBox.innerHTML = '', 4000);
        }

        function deleteUser(id) {
            if (!confirm("Bạn có chắc muốn xoá user này?")) return;

            fetch(`/api/users/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'Authorization': 'Bearer ' + token
                    }
                })
                .then(() => {
                    showAlert('Đã xoá user');
                    loadUsers();
                });
        }

        loadUsers();
    </script>
</body>

</html>
