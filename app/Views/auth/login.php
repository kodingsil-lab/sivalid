<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login Admin - SIVALID</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f4f6f8;
            color: #222;
        }

        .login-wrapper {
            width: 100%;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-card {
            width: 360px;
            background: #fff;
            border: 1px solid #ddd;
            padding: 28px;
            box-sizing: border-box;
        }

        .login-card h1 {
            margin: 0 0 4px;
            font-size: 24px;
        }

        .login-card p {
            margin: 0 0 24px;
            font-size: 14px;
            color: #666;
        }

        .form-group {
            margin-bottom: 14px;
        }

        label {
            display: block;
            margin-bottom: 6px;
            font-size: 14px;
            font-weight: bold;
        }

        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #bbb;
            box-sizing: border-box;
            font-size: 14px;
        }

        button {
            width: 100%;
            padding: 10px;
            border: 0;
            background: #1f4e79;
            color: #fff;
            font-size: 14px;
            cursor: pointer;
        }

        button:hover {
            background: #173a5a;
        }

        .alert {
            padding: 10px;
            margin-bottom: 14px;
            font-size: 14px;
            border: 1px solid transparent;
        }

        .alert-error {
            background: #fdecea;
            color: #9f1c1c;
            border-color: #f5c2c0;
        }

        .alert-success {
            background: #eaf7ea;
            color: #236b23;
            border-color: #b9e3b9;
        }

        .login-footer {
            margin-top: 18px;
            font-size: 12px;
            color: #777;
            text-align: center;
        }
    </style>
</head>
<body>

<div class="login-wrapper">
    <div class="login-card">
        <h1>SIVALID</h1>
        <p>Sistem Informasi Validasi Instrumen Penelitian</p>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-error">
                <?= esc(session()->getFlashdata('error')) ?>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success">
                <?= esc(session()->getFlashdata('success')) ?>
            </div>
        <?php endif; ?>

        <form action="<?= base_url('login') ?>" method="post">
            <?= csrf_field() ?>

            <div class="form-group">
                <label for="email">Email Admin</label>
                <input
                    type="email"
                    name="email"
                    id="email"
                    value="<?= old('email') ?>"
                    required
                    autofocus
                >
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input
                    type="password"
                    name="password"
                    id="password"
                    required
                >
            </div>

            <button type="submit">Login</button>
        </form>

        <div class="login-footer">
            Login awal: admin@sivalid.test / admin123
        </div>
    </div>
</div>

</body>
</html>