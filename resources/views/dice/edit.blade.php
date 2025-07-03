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
                    <h5 class="mb-0">Chỉnh sửa bảng</h5>
                    <a href="{{ route('dice.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Quay lại
                    </a>
                </div>

                <div class="card-body">
                    <form action="{{ route('dice.update', $dice) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="name" class="form-label">Tên cấu hình</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                   id="name" name="name" value="{{ old('name', $dice->name) }}" required>
                            @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="chat_id" class="form-label">Chat Id Telegram</label>
                            <input type="text" class="form-control @error('chat_id') is-invalid @enderror"
                                   id="chat_id" name="chat_id" value="{{ old('chat_id', $dice->chat_id) }}">
                            @error('chat_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="u1" class="form-label">Người chơi 1</label>
                            <input type="text" class="form-control @error('u1') is-invalid @enderror"
                                   id="u1" name="u1" value="{{ old('u1', $dice->u1) }}" required>
                            @error('u1')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="u2" class="form-label">Người chơi 2</label>
                            <input type="text" class="form-control @error('u2') is-invalid @enderror"
                                   id="u2" name="u2" value="{{ old('u2', $dice->u2) }}" required>
                            @error('u2')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="u3" class="form-label">Người chơi 3</label>
                            <input type="text" class="form-control @error('u3') is-invalid @enderror"
                                   id="u3" name="u3" value="{{ old('u3', $dice->u3) }}" required>
                            @error('u3')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="u4" class="form-label">Người chơi 4</label>
                            <input type="text" class="form-control @error('u4') is-invalid @enderror"
                                   id="u4" name="u4" value="{{ old('u4', $dice->u4) }}" required>
                            @error('u4')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Cập nhật
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
