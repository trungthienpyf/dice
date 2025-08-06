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

                                <div class="mb-3">
                                    <label for="role" class="form-label">Role</label>
                                    <label class="form-label"><b>{{ $userRoles[0] ?? '' }}</b></label>

                                </div>

                                    <div class="col-12">
    <div class="permission-group mb-3">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <strong>Danh sách quyền</strong>
                <button type="button" class="btn btn-sm btn-outline-primary" id="select-all-template">
                    Chọn tất cả
                </button>
            </div>
            <div class="card-body row">
                @foreach ($template as $permission)
                    <div class="form-check col-md-4">
                        <input class="form-check-input permission-checkbox"
                            type="checkbox" name="permissions[]"
                            value="{{ $permission }}"
                            id="permission-{{ Str::slug($permission) }}"
                            {{ in_array($permission, $userPermissions->toArray()) ? 'checked' : '' }}>
                        <label class="form-check-label"
                            for="permission-{{ Str::slug($permission) }}">
                            {{ $permission }}
                        </label>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>





                                <div class="col-12">
                                    <label class="mt-3">Dice Tables</label>

                                    {{-- Nút chọn tất cả --}}
                                    <div class="form-check mb-3">
                                        <input type="checkbox" id="checkAllDice" class="form-check-input">
                                        <label class="form-check-label fw-bold" for="checkAllDice">Chọn tất cả</label>
                                    </div>

                                    {{-- Các card theo dice parent --}}
                                    @foreach ($diceTablesGroupedByParent as $parentId => $tables)
                                        <div class="card mb-3">
                                            <div class="card-header d-flex justify-content-between align-items-center">
                                                <strong>
                                                    Dice: {{ $tables->first()->diceParent->name ?? 'Không xác định' }}
                                                </strong>
                                                <button type="button"
                                                    class="btn btn-sm btn-outline-primary select-all-dice-btn"
                                                    data-parent="{{ $parentId }}">
                                                    Chọn tất cả
                                                </button>
                                            </div>
                                            <div class="card-body row">
                                                @foreach ($tables as $table)
                                                    <div class="col-md-4">
                                                        <div class="form-check">
                                                            <input
                                                                class="form-check-input dice-checkbox parent-{{ $parentId }}-checkbox"
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
        document.addEventListener('DOMContentLoaded', function() {
            // Nút chọn tất cả toàn bộ Dice Tables
            const checkAllDice = document.getElementById('checkAllDice');
            checkAllDice?.addEventListener('change', function() {
                const allCheckboxes = document.querySelectorAll('.dice-checkbox');
                allCheckboxes.forEach(cb => cb.checked = this.checked);
            });

            // Nút chọn tất cả từng nhóm (theo parent)
            document.querySelectorAll('.select-all-dice-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const parentId = this.dataset.parent;
                    const checkboxes = document.querySelectorAll(`.parent-${parentId}-checkbox`);
                    const allChecked = Array.from(checkboxes).every(cb => cb.checked);
                    checkboxes.forEach(cb => cb.checked = !allChecked);
                });
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const roleSelect = document.getElementById('role');
            const permissionGroups = document.querySelectorAll('.permission-group');

            // Hiện permissions theo role
            function updatePermissionVisibility() {
                const selectedRole = roleSelect.value;
                permissionGroups.forEach(group => {
                    group.style.display = group.id === `permissions-${selectedRole}` ? 'block' : 'none';
                });
            }

            if (roleSelect) {
                roleSelect.addEventListener('change', updatePermissionVisibility);
                updatePermissionVisibility();
            }

            // Nút chọn tất cả
            document.querySelectorAll('.select-all-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const role = this.dataset.role;
                    const checkboxes = document.querySelectorAll(`.${role}-checkbox`);

                    // Kiểm tra nếu tất cả đã check thì sẽ uncheck, ngược lại sẽ check hết
                    const allChecked = Array.from(checkboxes).every(cb => cb.checked);
                    checkboxes.forEach(cb => cb.checked = !allChecked);
                });
            });
        });
    </script>
    <script>
    document.getElementById('select-all-template').addEventListener('click', function () {
        const checkboxes = document.querySelectorAll('.permission-checkbox');
        const allChecked = Array.from(checkboxes).every(cb => cb.checked);

        checkboxes.forEach(cb => cb.checked = !allChecked);
        this.textContent = allChecked ? 'Chọn tất cả' : 'Bỏ chọn tất cả';
    });
</script>


</body>

</html>
