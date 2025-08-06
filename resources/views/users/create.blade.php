<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        body {
            background-color: #f8f9fa;
            padding: 30px 10px;
        }

        .card {
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.05);
        }

        .form-label {
            font-weight: 500;
        }

        .select2-container--default .select2-selection--single {
            height: 38px;
            padding: 6px 12px;
            border-radius: 0.375rem;
            border: 1px solid #ced4da;
        }

        .select2-container .select2-selection--single .select2-selection__rendered {
            line-height: 24px;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px;
            top: 1px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                <h5 class="mb-0">Tạo mới User</h5>
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

                <form action="{{ route('users.store') }}" method="POST">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="name">Họ tên</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                name="name" id="name" value="{{ old('name') }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="username">Username</label>
                            <input type="text" class="form-control @error('username') is-invalid @enderror"
                                name="username" id="username" value="{{ old('username') }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="password">Password</label>
                            <input type="text" class="form-control @error('password') is-invalid @enderror"
                                name="password" id="password">
                        </div>

                        {{-- <div class="col-md-6">
                            <label class="form-label" for="expired_at">Ngày hết hạn</label>
                            <input type="date" class="form-control" name="expired_at"
                                value="{{ old('expired_at') }}">
                        </div> --}}

                        <div class="col-12">
                            <label for="role" class="form-label">Vai trò</label>
                            <select class="form-select" id="role-select" name="role">
                                <option value="">-- Chọn vai trò --</option>
                                @foreach ($lower as $role => $permissions)
                                    <option value="{{ $role }}">{{ ucfirst($role) }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12">

                            @foreach ($lower as $role => $permissions)
                                <div class="permission-group mb-3" id="permissions-{{ $role }}"
                                    style="display: none;">
                                    <div class="card">
                                        <div class="card-header d-flex justify-content-between align-items-center">
                                            <strong>Chọn quyền</strong>
                                            <button type="button" class="btn btn-sm btn-outline-primary select-all-btn"
                                                data-role="{{ $role }}">
                                                Chọn tất cả
                                            </button>
                                        </div>
                                        <div class="card-body row">
                                            @foreach ($permissions as $permission)
                                                <div class="form-check col-md-4">
                                                    <input
                                                        class="form-check-input permission-checkbox {{ $role }}-checkbox"
                                                        type="checkbox" name="permissions[]"
                                                        value="{{ $permission }}"
                                                        id="{{ $role }}-{{ Str::slug($permission) }}">
                                                    <label class="form-check-label"
                                                        for="{{ $role }}-{{ Str::slug($permission) }}">
                                                        {{ $permission }}
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endforeach


                        </div>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-bold">Quyền truy cập Dice</label>

                        @foreach ($diceTablesGroupedByParent as $parentId => $tables)
                            <div class="mb-3" id="dice-group-{{ $parentId }}">
                                <div class="card">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <strong>
                                            Dice: {{ $tables->first()->diceParent->name ?? 'Không xác định' }}
                                        </strong>
                                        <button type="button"
                                            class="btn btn-sm btn-outline-primary select-all-dice-btn"
                                            data-parent-id="{{ $parentId }}">
                                            Chọn tất cả Dice
                                        </button>
                                    </div>

                                    <div class="card-body row">
                                        @foreach ($tables as $table)
                                            <div class="form-check col-md-4">
                                                <input class="form-check-input dice-checkbox" type="checkbox"
                                                    name="dice_tables[{{ $table->id }}]" value="1"
                                                    data-parent-id="{{ $parentId }}"
                                                    id="dice_table_{{ $table->id }}">
                                                <label class="form-check-label" for="dice_table_{{ $table->id }}">
                                                    {{ $table->id }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>


                    <div class="col-12 d-flex justify-content-end">
                        <a href="{{ route('users.index') }}" class="btn btn-secondary me-2">
                            <i class="fas fa-times"></i> Hủy
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Tạo User
                        </button>
                    </div>
            </div>
            </form>
        </div>
    </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        // Ẩn/hiện nhóm quyền theo role
        document.getElementById('role-select').addEventListener('change', function() {
            const selectedRole = this.value;

            // Ẩn tất cả
            document.querySelectorAll('.permission-group').forEach(div => div.style.display = 'none');

            if (selectedRole) {
                const group = document.getElementById(`permissions-${selectedRole}`);
                if (group) {
                    group.style.display = 'block';
                }
            }
        });

        // Nút chọn tất cả
        document.querySelectorAll('.select-all-btn').forEach(button => {
            button.addEventListener('click', function() {
                const role = this.dataset.role;
                const checkboxes = document.querySelectorAll(`.${role}-checkbox`);
                const allChecked = Array.from(checkboxes).every(cb => cb.checked);

                checkboxes.forEach(cb => cb.checked = !allChecked);

                this.textContent = allChecked ? 'Chọn tất cả' : 'Bỏ chọn tất cả';
            });
        });
    </script>
    <script>
    document.querySelectorAll('.select-all-dice-btn').forEach(button => {
        button.addEventListener('click', function () {
            const parentId = this.dataset.parentId;
            const checkboxes = document.querySelectorAll(
                `.dice-checkbox[data-parent-id="${parentId}"]`
            );

            const allChecked = Array.from(checkboxes).every(cb => cb.checked);
            checkboxes.forEach(cb => cb.checked = !allChecked);

            this.textContent = allChecked ? 'Chọn tất cả Dice' : 'Bỏ chọn tất cả Dice';
        });
    });
</script>


</body>

</html>
