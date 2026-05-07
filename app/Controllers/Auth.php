<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;

class Auth extends BaseController
{
    public function login()
    {
        if (session()->get('isLoggedIn')) {
            return redirect()->to(base_url('admin/dashboard'));
        }

        return view('auth/login');
    }

    public function attemptLogin()
    {
        $email    = trim((string) $this->request->getPost('email'));
        $password = (string) $this->request->getPost('password');

        if ($email === '' || $password === '') {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Email dan password wajib diisi.');
        }

        $userModel = new UserModel();
        $user = $userModel->where('email', $email)->first();

        if (!$user) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Email tidak ditemukan.');
        }

        if ($user['status'] !== 'aktif') {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Akun tidak aktif.');
        }

        if (!password_verify($password, $user['password'])) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Password salah.');
        }

        session()->set([
            'user_id'    => $user['id'],
            'user_name'  => $user['name'],
            'user_email' => $user['email'],
            'user_role'  => $user['role'],
            'isLoggedIn' => true,
        ]);

        return redirect()->to(base_url('admin/dashboard'));
    }

    public function logout()
    {
        session()->destroy();

        return redirect()
            ->to(base_url('login'))
            ->with('success', 'Anda berhasil logout.');
    }
}