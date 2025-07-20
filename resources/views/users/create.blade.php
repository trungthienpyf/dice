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

                        <div class="col-md-6">
                            <label class="form-label" for="expired_at">Ngày hết hạn</label>
                            <input type="date" class="form-control" name="expired_at"
                                   value="{{ old('expired_at') }}">
                        </div>

                        <div class="col-12">
                            <label class="form-label" for="role">Phân quyền</label>
                            <select name="role" id="role" class="form-select select2">
                                @foreach ($roles as $role)
                                    <option value="{{ $role->name }}">{{ $role->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Permissions</label>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="select_all_permissions">
                                <label class="form-check-label fw-bold" for="select_all_permissions">
                                    Chọn tất cả permissions
                                </label>
                            </div>
                            <div class="row">
                                @foreach ($permissions as $permission)
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input permission-checkbox"
                                                   name="permissions[]" value="{{ $permission->name }}"
                                                   id="perm_{{ $permission->id }}">
                                            <label class="form-check-label" for="perm_{{ $permission->id }}">
                                                {{ $permission->name }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold">Quyền truy cập Dice</label>
                            @foreach ($diceTablesGroupedByParent as $parentId => $tables)
                                <div class="mb-3">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <label class="form-label mb-1">
                                            Dice: {{ $tables->first()->diceParent->name ?? 'Không xác định' }}
                                        </label>
                                        <div class="form-check">
                                            <input class="form-check-input select-all-dice"
                                                   type="checkbox" id="select_all_dice_{{ $parentId }}"
                                                   data-parent-id="{{ $parentId }}">
                                            <label class="form-check-label small" for="select_all_dice_{{ $parentId }}">
                                                Chọn tất cả Dice
                                            </label>
                                        </div>
                                    </div>
                                    <div class="row">
                                        @foreach ($tables as $table)
                                            <div class="col-md-4">
                                                <div class="form-check">
                                                    <input class="form-check-input dice-checkbox"
                                                           type="checkbox"
                                                           name="dice_tables[{{ $table->id }}]" value="1"
                                                           data-parent-id="{{ $parentId }}"
                                                           id="dice_table_{{ $table->id }}">
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
        $(document).ready(function () {
            $('.select2').select2({
                placeholder: "Chọn vai trò",
                allowClear: true,
                width: '100%'
            });

            // Chọn tất cả permission
            $('#select_all_permissions').change(function () {
                $('.permission-checkbox').prop('checked', $(this).is(':checked'));
            });

            // Chọn tất cả Dice theo parentId
            $('.select-all-dice').change(function () {
                let parentId = $(this).data('parent-id');
                $('.dice-checkbox[data-parent-id="' + parentId + '"]').prop('checked', $(this).is(':checked'));
            });
        });
    </script>
</body>

</html>
