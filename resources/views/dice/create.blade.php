<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Dice Session</title>
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
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .row-input {
            background-color: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 10px;
        }

        .select2-container .select2-selection--single {
            height: calc(2.25rem + 2px);
            padding: 0.375rem 0.75rem;
            font-size: 1rem;
            line-height: 1.5;
            border-radius: 0.375rem;
            border: 1px solid #ced4da;
            box-sizing: border-box;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 1.5;
            padding-left: 0;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: calc(2.25rem + 2px);
            top: 0;
            right: 0.25rem;
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

            .row-input .row {
                flex-direction: column;
            }

            .row-input .col {
                margin-bottom: 10px;
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

            .select2-container {
                width: 100% !important;
            }
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-12 col-md-10 col-lg-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                        <h5 class="mb-0">Tạo mới bảng chơi</h5>
                        <a href="{{ route('dice.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Quay lại
                        </a>
                    </div>

                    <div class="card-body">
                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('dice.store') }}" method="POST">
                            @csrf
                            <div class="form-group row g-3">
                                <div class="col-12 col-md-6">
                                    <label for="date">Title</label>
                                    <input type="text" class="form-control @error('date') is-invalid @enderror"
                                           id="date" name="name" value="Bảng ghi {{ $session->id ?? '' }}" required>
                                </div>

                                <div class="col-12 col-md-6">
                                    <label for="chat_id">Chat Id telegram</label>
                                    <input type="text" class="form-control @error('chat_id') is-invalid @enderror"
                                           id="chat_id" name="chat_id" value="">
                                </div>

                                <div class="col-12 col-md-6">
                                    <label for="config_id">Cấu hình</label>
                                    <select name="config_id" id="config_id" class="select2 form-control">
                                        @foreach($configs as $index => $config)
                                            <option value="{{ $config->id }}" {{ $index === 0 ? 'selected' : '' }}>
                                                {{ $config->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group mt-4">
                                <label>Người chơi</label>
                                <div id="rows-container">
                                    <div class="row-input">
                                        <div class="row g-2">
                                            <div class="col-12 col-sm-6 col-md-3">
                                                <input type="text" class="form-control" name="u1" placeholder="Người 1" required>
                                            </div>
                                            <div class="col-12 col-sm-6 col-md-3">
                                                <input type="text" class="form-control" name="u2" placeholder="Người 2" required>
                                            </div>
                                            <div class="col-12 col-sm-6 col-md-3">
                                                <input type="text" class="form-control" name="u3" placeholder="Người 3" required>
                                            </div>
                                            <div class="col-12 col-sm-6 col-md-3">
                                                <input type="text" class="form-control" name="u4" placeholder="Người 4" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group mt-4">
                                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                    <a href="{{ route('dice.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> Cancel
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Tạo bảng
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#config_id').select2({
                placeholder: "-- Chọn cấu hình --",
                allowClear: true,
                width: '100%'
            });
        });
    </script>
</body>
</html>
