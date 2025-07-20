<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <style>
        body {
            background-color: #f8f9fa;
            padding: 15px;
        }

        .card {
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-12 col-md-10 col-lg-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                        <h5 class="mb-0">Chỉnh sửa user</h5>
                        <a href="{{ route('users.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Quay lại
                        </a>
                    </div>

                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('users.update', $user->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="name">Họ tên</label>
                                    <input type="text" name="name" id="name" class="form-control"
                                        value="{{ old('name', $user->name) }}">
                                </div>

                                <div class="col-md-6">
                                    <label for="username">Tên đăng nhập</label>
                                    <input type="text" name="username" id="username" class="form-control"
                                        value="{{ old('username', $user->username) }}">
                                </div>

                                <div class="col-md-6">
                                    <label for="password">Mật khẩu (để trống nếu không đổi)</label>
                                    <input type="password" name="password" id="password" class="form-control">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label" for="expired_at">Ngày hết hạn</label>
                                    <input type="date" class="form-control @error('expired_at') is-invalid @enderror"
                                        name="expired_at" id="expired_at"
                                        value="{{ old('expired_at', $user->expired_at ? \Carbon\Carbon::parse($user->expired_at)->format('Y-m-d') : '') }}">
                                </div>

                                <div class="col-md-6">
                                    <label for="role">Vai trò</label>
                                    <select name="role" id="role" class="form-control select2">
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->name }}"
                                                {{ $user->hasRole($role->name) ? 'selected' : '' }}>
                                                {{ $role->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-12">
                                    <label>Quyền</label>
                                    <div class="form-check mb-2">
                                        <input type="checkbox" id="checkAllPermissions" class="form-check-input">
                                        <label class="form-check-label fw-bold" for="checkAllPermissions">Chọn tất
                                            cả</label>
                                    </div>

                                    <div class="row">
                                        @foreach ($permissions as $permission)
                                            <div class="col-md-4">
                                                <div class="form-check">
                                                    <input class="form-check-input permission-checkbox" type="checkbox"
                                                        name="permissions[]" value="{{ $permission->name }}"
                                                        id="perm_{{ $permission->id }}"
                                                        {{ $userPermissions->contains($permission->name) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="perm_{{ $permission->id }}">
                                                        {{ $permission->name }}
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="col-12">
                                    <label class="mt-3">Dice Tables</label>
                                    <div class="form-check mb-2">
                                        <input type="checkbox" id="checkAllDice" class="form-check-input">
                                        <label class="form-check-label fw-bold" for="checkAllDice">Chọn tất cả</label>
                                    </div>

                                    @foreach ($diceTablesGroupedByParent as $parentId => $tables)
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">
                                                Dice: {{ $tables->first()->diceParent->name ?? 'Không xác định' }}
                                            </label>
                                            <div class="row">
                                                @foreach ($tables as $table)
                                                    <div class="col-md-4">
                                                        <div class="form-check">
                                                            <input class="form-check-input dice-checkbox"
                                                                type="checkbox" name="dice_tables[{{ $table->id }}]"
                                                                value="1" id="dice_table_{{ $table->id }}"
                                                                {{ in_array($table->id, $checkedDiceTableIds) ? 'checked' : '' }}>
                                                            <label class="form-check-label"
                                                                for="dice_table_{{ $table->id }}">
                                                                {{ $table->id }}
                                                            </label>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="form-group mt-4">
                                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                    <a href="{{ route('users.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> Huỷ
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Cập nhật user
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div> <!-- /.card-body -->
                </div>
            </div>
        </div>
    </div>

    <!-- JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            $('.select2').select2({
                width: '100%'
            });

            $('#checkAllPermissions').on('change', function() {
                $('.permission-checkbox').prop('checked', this.checked);
            });

            $('#checkAllDice').on('change', function() {
                $('.dice-checkbox').prop('checked', this.checked);
            });
        });
    </script>
</body>

</html>
