<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chỉnh sửa cấu hình</title>
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

        @media (max-width: 768px) {
            .card-header {
                flex-direction: column;
                gap: 10px;
            }

            .card-header .btn {
                width: 100%;
            }

            .form-label {
                font-size: 0.9rem;
            }

            .form-control {
                font-size: 0.9rem;
            }
        }

        @media (max-width: 576px) {
            body {
                padding: 10px;
            }

            .container {
                padding: 0;
            }

            .card {
                border-radius: 0;
            }
        }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                    <h5 class="mb-0">Chỉnh sửa cấu hình</h5>
                    <a href="{{ route('dice.configs.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Quay lại
                    </a>
                </div>

                <div class="card-body">
                    <form action="{{ route('dice.configs.update', $config) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="name" class="form-label">Tên cấu hình</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                   id="name" name="name" value="{{ old('name', $config->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="td" class="form-label">Tiền 1 chi</label>
                            <input type="number" class="form-control @error('td') is-invalid @enderror"
                                   id="td" name="td" value="{{ old('td', $config->td) }}" required>
                            @error('td')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="cc" class="form-label">Số chi để sâu</label>
                            <input type="number" class="form-control @error('cc') is-invalid @enderror"
                                   id="cc" name="cc" value="{{ old('cc', $config->cc) }}" required>
                            @error('cc')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="ts" class="form-label">Tiền sâu</label>
                            <input type="number" class="form-control @error('ts') is-invalid @enderror"
                                   id="ts" name="ts" value="{{ old('ts', $config->ts) }}" required>
                            @error('ts')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>


                        <div class="mb-3">
                            <label for="same_row" class="form-label">Tiền sâu</label>
                            <input type="number" class="form-control @error('same_row') is-invalid @enderror"
                                   id="same_row" name="same_row" value="{{ old('same_row', $config->same_row) }}" required>
                            @error('same_row')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Cập nhật cấu hình
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
