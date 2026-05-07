<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\AuditLogService;
use App\Models\UserModel;

class AdminUsers extends BaseController
{
    protected UserModel $userModel;
    protected AuditLogService $auditLog;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->auditLog  = new AuditLogService();
    }

    public function index()
    {
        $data = [
            'title' => 'Manajemen User Admin',
            'users' => $this->userModel->orderBy('id', 'ASC')->findAll(),
        ];

        return view('admin/admin_users/index', $data);
    }

    public function new()
    {
        $data = [
            'title' => 'Tambah User Admin',
            'user'  => null,
        ];

        return view('admin/admin_users/form', $data);
    }

    public function create()
    {
        $rules = [
            'name'             => 'required|max_length[150]',
            'email'            => 'required|valid_email|is_unique[users.email]',
            'password'         => 'required|min_length[8]',
            'password_confirm' => 'required|matches[password]',
        ];

        if (!$this->validate($rules)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $this->userModel->insert([
            'name'     => trim($this->request->getPost('name')),
            'email'    => trim($this->request->getPost('email')),
            'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'role'     => 'admin',
            'status'   => 'aktif',
        ]);

        $newId = $this->userModel->getInsertID();

        $this->auditLog->log(
            AuditLogService::ACTION_CREATE_INSTRUMENT,
            'user',
            $newId,
            'Tambah user admin: ' . trim($this->request->getPost('email'))
        );

        return redirect()
            ->to(base_url('admin/admin-users'))
            ->with('success', 'User admin berhasil ditambahkan.');
    }

    public function edit($id = null)
    {
        $userId = (int) $id;
        $user   = $this->userModel->find($userId);

        if (!$user) {
            return redirect()
                ->to(base_url('admin/admin-users'))
                ->with('error', 'User tidak ditemukan.');
        }

        $data = [
            'title' => 'Edit User Admin',
            'user'  => $user,
        ];

        return view('admin/admin_users/form', $data);
    }

    public function update($id = null)
    {
        $userId = (int) $id;
        $user   = $this->userModel->find($userId);

        if (!$user) {
            return redirect()
                ->to(base_url('admin/admin-users'))
                ->with('error', 'User tidak ditemukan.');
        }

        $rules = [
            'name' => 'required|max_length[150]',
        ];

        $newPassword = $this->request->getPost('password');

        if ($newPassword !== '' && $newPassword !== null) {
            $rules['password']         = 'min_length[8]';
            $rules['password_confirm'] = 'matches[password]';
        }

        if (!$this->validate($rules)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $updateData = [
            'name' => trim($this->request->getPost('name')),
        ];

        if ($newPassword !== '' && $newPassword !== null) {
            $updateData['password'] = password_hash($newPassword, PASSWORD_DEFAULT);
        }

        $this->userModel->update($userId, $updateData);

        $this->auditLog->log(
            AuditLogService::ACTION_UPDATE_INSTRUMENT,
            'user',
            $userId,
            'Edit user admin: ' . $user['email']
        );

        return redirect()
            ->to(base_url('admin/admin-users'))
            ->with('success', 'Data user berhasil diperbarui.');
    }

    public function toggleStatus($id = null)
    {
        $userId  = (int) $id;
        $user    = $this->userModel->find($userId);
        $session = session();

        if (!$user) {
            return redirect()
                ->to(base_url('admin/admin-users'))
                ->with('error', 'User tidak ditemukan.');
        }

        // Cegah admin menonaktifkan akun dirinya sendiri
        if ((int) $session->get('user_id') === $userId) {
            return redirect()
                ->to(base_url('admin/admin-users'))
                ->with('error', 'Tidak dapat menonaktifkan akun Anda sendiri.');
        }

        $newStatus = $user['status'] === 'aktif' ? 'nonaktif' : 'aktif';

        $this->userModel->update($userId, ['status' => $newStatus]);

        $this->auditLog->log(
            AuditLogService::ACTION_UPDATE_INSTRUMENT,
            'user',
            $userId,
            'Status user ' . $user['email'] . ' diubah menjadi ' . $newStatus
        );

        return redirect()
            ->to(base_url('admin/admin-users'))
            ->with('success', 'Status user berhasil diubah menjadi ' . $newStatus . '.');
    }
}
