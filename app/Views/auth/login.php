<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - SIVALID</title>
    <link rel="icon" href="<?= sivalid_favicon_url() ?>">
    <link rel="stylesheet" href="<?= base_url('assets/vendor/tabler/css/tabler.min.css') ?>">
    <style>
        body {
            background: #f0f4f8;
        }
        .sv-brand-logo {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 12px;
            box-shadow: 0 4px 12px rgba(29,111,184,.25);
            overflow: hidden;
            border: 1px solid rgba(29,111,184,.12);
        }
        .sv-brand-logo img {
            display: block;
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
    </style>
</head>
<body class="d-flex flex-column min-vh-100 justify-content-center">

<div class="container-tight py-5">
    <div class="card card-md shadow-sm">
        <div class="card-body">
            <div class="text-center mb-4">
                <div class="sv-brand-logo">
                    <img src="<?= sivalid_logo_url() ?>" alt="Logo SIVALID">
                </div>
                <h2 class="fw-bold mb-0 mt-2" style="letter-spacing:-.5px;">SIVALID</h2>
                <p class="text-muted mt-1 mb-0" style="font-size:13.5px;">Sistem Validasi Instrumen Penelitian</p>
            </div>
            <h3 class="card-title text-center mb-4">Masuk ke akun Anda</h3>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible" role="alert">
                    <div class="d-flex">
                        <div>
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24"
                                 viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <circle cx="12" cy="12" r="9"/>
                                <line x1="12" y1="8" x2="12" y2="12"/>
                                <line x1="12" y1="16" x2="12.01" y2="16"/>
                            </svg>
                        </div>
                        <div><?= esc(session()->getFlashdata('error')) ?></div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success" role="alert">
                    <div class="d-flex">
                        <div>
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24"
                                 viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <circle cx="12" cy="12" r="9"/>
                                <path d="M9 12l2 2l4-4"/>
                            </svg>
                        </div>
                        <div><?= esc(session()->getFlashdata('success')) ?></div>
                    </div>
                </div>
            <?php endif; ?>

            <form action="<?= base_url('login') ?>" method="post" autocomplete="off" novalidate>
                <?= csrf_field() ?>

                <div class="mb-3">
                    <label class="form-label" for="email">Email</label>
                    <input
                        type="email"
                        name="email"
                        id="email"
                        class="form-control"
                        placeholder="nama@contoh.com"
                        value="<?= old('email') ?>"
                        required
                        autofocus
                    >
                </div>

                <div class="mb-3">
                    <label class="form-label" for="password">Password</label>
                    <input
                        type="password"
                        name="password"
                        id="password"
                        class="form-control"
                        placeholder="Password Anda"
                        required
                    >
                </div>

                <div class="form-footer">
                    <button type="submit" class="btn btn-primary w-100">
                        Masuk
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="text-center text-muted mt-3" style="font-size:12px;">
        Login awal: <code>admin@sivalid.test</code> / <code>admin123</code>
    </div>
</div>

</body>
</html>
