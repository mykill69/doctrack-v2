<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Login | Document Tracking System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap & FontAwesome -->
    <link rel="stylesheet" href="{{ asset('template/assets/modules/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('template/assets/modules/fontawesome/css/all.min.css') }}">

    <!-- CodiePie CSS -->
    <link rel="stylesheet" href="{{ asset('template/assets/css/style.min.css') }}">
    <link rel="stylesheet" href="{{ asset('template/assets/css/components.min.css') }}">

    <link rel="shortcut icon" href="{{ asset('template/img/cpsu_logo.png') }}">

    <style>
        /* ===== Background ===== */
        body.login-page {
            background: linear-gradient(135deg, #04401f, #6c9076);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 15px;
        }

        /* ===== Glass Card (LIGHTER) ===== */
        .login-box {
            width: 100%;
            max-width: 420px;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.30);
            /* LIGHTER glass */
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-radius: 16px;
            border: 1px solid rgba(255, 255, 255, 0.45);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.20);
            padding: 35px;
            color: #1f2d3d;
            /* darker text for readability */
        }

        /* Fallback for browsers without backdrop-filter */
        @supports not ((backdrop-filter: blur(10px)) or (-webkit-backdrop-filter: blur(10px))) {
            .glass-card {
                background: rgba(255, 255, 255, 0.9);
                color: #212529;
            }
        }

        /* ===== Logo ===== */
        .login-logo {
            text-align: center;
            margin-bottom: 20px;
        }

        .login-logo img {
            width: 150px;
            margin-bottom: 10px;
            top: 0;
        }

        .login-logo h4 {
            font-weight: 600;
            margin: 0;
            font-size: 18px;
            color: #1f2d3d;
        }

        .login-box-msg {
            text-align: center;
            font-size: 14px;
            color: #495057;
            margin-bottom: 25px;
        }

        /* ===== Form ===== */
        .form-control {
            height: 48px;
            background: rgba(255, 255, 255, 0.95);
            border: 1px solid rgba(0, 0, 0, 0.08);
            border-radius: 10px;
            font-size: 14px;
            color: #212529;
        }

        .form-control::placeholder {
            color: #6c757d;
        }

        .form-control:focus {
            box-shadow: 0 0 0 2px rgba(4, 64, 31, 0.25);
        }

        .input-group-text {
            background: rgba(4, 64, 31, 0.85);
            border: none;
            color: #fff;
            border-radius: 0 10px 10px 0;
        }

        .btn-primary {
            background-color: #04401f;
            border: none;
            height: 48px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 15px;
        }

        .btn-primary:hover {
            background-color: #036c35;
        }

        .icheck-success label {
            font-size: 13px;
            color: #1f2d3d;
        }

        .form-text {
            font-size: 12px;
        }

        /* ===== Alerts ===== */
        .alert {
            background: rgba(255, 255, 255, 0.95);
            color: #212529;
            border-radius: 10px;
            font-size: 13px;
            border: none;
        }

        /* ===== Responsive Tweaks ===== */
        @media (max-width: 480px) {
            .glass-card {
                padding: 25px;
            }

            .login-logo img {
                width: 70px;
            }

            .login-logo h4 {
                font-size: 16px;
            }
        }
      
    </style>

</head>

<body class="hold-transition login-page">

    <div class="login-box">
        <div class="glass-card">

            <!-- Logo -->
            <div class="login-logo">
                <img src="{{ asset('template/img/cpsu_logo.png') }}" alt="CPSU Logo">
                <h4 style="font-size: 24px;color:#ffff;">Document Tracking System</h4>
                <p style="font-size: 12px;color:#ffff;">version 2.0</p>
            </div>

            <p class="login-box-msg">Sign in to start your session</p>

            <!-- Login Form -->
            <form action="{{ route('postLogin') }}" method="post">
                @csrf

                <!-- Email -->
                <div class="input-group mb-3">
                    <input type="text" name="email" class="form-control"
                        placeholder="Institutional Email"
                        value="{{ old('email') }}" autofocus>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <i class="fas fa-user"></i>
                        </div>
                    </div>
                </div>
                @error('email')
                    <span class="form-text text-warning">{{ $message }}</span>
                @enderror

                <!-- Password -->
                <div class="input-group mb-3">
                    <input type="password" name="password" id="myInput"
                        class="form-control" placeholder="Password">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <i class="fas fa-lock"></i>
                        </div>
                    </div>
                </div>
                @error('password')
                    <span class="form-text text-warning">{{ $message }}</span>
                @enderror

                <!-- Options -->
                <div class="mb-3">
                    <div class="icheck-success">
                        <input type="checkbox" id="show" onclick="togglePassword()">
                        <label for="show">Show Password</label>
                    </div>
                </div>

                <!-- Button -->
                <button type="submit" class="btn btn-primary btn-block">
                    <i class="fas fa-sign-in-alt"></i> Sign In
                </button>
            </form>

            <!-- Alerts -->
            @if (session('error'))
                <div class="alert alert-danger mt-3">
                    <i class="fas fa-exclamation-triangle"></i>
                    {{ session('error') }}
                </div>
            @endif

            @if (session('success'))
                <div class="alert alert-success mt-3">
                    <i class="fas fa-check"></i>
                    {{ session('success') }}
                </div>
            @endif

        </div>
    </div>

    <!-- JS -->
    <script src="{{ asset('template/assets/bundles/lib.vendor.bundle.js') }}"></script>
    <script src="{{ asset('template/js/CodiePie.js') }}"></script>

    <script>
        function togglePassword() {
            const input = document.getElementById("myInput");
            input.type = input.type === "password" ? "text" : "password";
        }
    </script>

</body>

</html>
