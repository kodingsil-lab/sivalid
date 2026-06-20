<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\AuditLogService;
use App\Models\SettingModel;
use App\Models\UserModel;

class AdminUsers extends BaseController
{
    protected UserModel $userModel;
    protected SettingModel $settingModel;
    protected AuditLogService $auditLog;

    public function __construct()
    {
        $this->userModel    = new UserModel();
        $this->settingModel = new SettingModel();
        $this->auditLog     = new AuditLogService();
    }

    public function index()
    {
        $perPage = config('Pager')->perPage;

        $data = [
            'title' => 'Manajemen User Admin',
            'users' => $this->userModel->orderBy('id', 'ASC')->paginate($perPage, 'admin_users'),
            'pager' => $this->userModel->pager,
            'pagerGroup' => 'admin_users',
        ];

        return view('admin/admin_users/index', $data);
    }

    public function show($id = null)
    {
        $userId = (int) $id;
        $user   = $this->userModel->find($userId);

        if (!$user) {
            return redirect()
                ->to(base_url('admin/admin-users'))
                ->with('error', 'User tidak ditemukan.');
        }

        $data = [
            'title'   => 'Detail User Admin',
            'user'    => $user,
            'profile' => $this->settingModel->getGroupValues('profile'),
        ];

        return view('admin/admin_users/show', $data);
    }

    public function new()
    {
        $data = [
            'title'   => 'Tambah User Admin',
            'user'    => null,
            'profile' => $this->settingModel->getGroupValues('profile'),
        ];

        return view('admin/admin_users/form', $data);
    }

    public function create()
    {
        $rules = [
            'name'             => 'required|max_length[150]',
            'email'            => 'required|valid_email|is_unique[users.email]',
            'role'             => 'required|in_list[superadmin,admin]',
            'password'         => 'required|min_length[8]',
            'password_confirm' => 'required|matches[password]',
        ];

        $rules = array_merge($rules, $this->profileValidationRules());

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
            'role'     => $this->normalizeRole((string) $this->request->getPost('role')),
            'status'   => 'aktif',
        ]);

        $newId = $this->userModel->getInsertID();
        $this->saveProfileFromRequest();

        $this->auditLog->log(
            AuditLogService::ACTION_CREATE_INSTRUMENT,
            'user',
            $newId,
            'Tambah user admin: ' . trim($this->request->getPost('email'))
        );

        return redirect()
            ->to(base_url('admin/admin-users/' . $newId))
            ->with('success', 'User admin dan profil penelitian berhasil ditambahkan.');
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
            'role' => 'required|in_list[superadmin,admin]',
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
            'role' => $this->normalizeRole((string) $this->request->getPost('role')),
        ];

        if (
            (string) ($user['role'] ?? '') === 'superadmin'
            && $updateData['role'] !== 'superadmin'
            && $this->countActiveSuperadmins() <= 1
        ) {
            return redirect()
                ->back()
                ->withInput()
                ->with('errors', ['role' => 'Superadmin terakhir tidak boleh diturunkan menjadi admin.']);
        }

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

        if (
            $newStatus === 'nonaktif'
            && (string) ($user['role'] ?? '') === 'superadmin'
            && $this->countActiveSuperadmins() <= 1
        ) {
            return redirect()
                ->to(base_url('admin/admin-users'))
                ->with('error', 'Superadmin terakhir tidak boleh dinonaktifkan.');
        }

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

    public function saveProfile($id = null)
    {
        $userId = (int) $id;
        $user   = $this->userModel->find($userId);

        if (!$user) {
            return redirect()
                ->to(base_url('admin/admin-users'))
                ->with('error', 'User tidak ditemukan.');
        }

        $rules = $this->profileValidationRules();

        if (!$this->validate($rules)) {
            return redirect()
                ->to(base_url('admin/admin-users/' . $userId))
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $this->saveProfileFromRequest();

        $this->auditLog->log(
            AuditLogService::ACTION_UPDATE_INSTRUMENT,
            'settings',
            $userId,
            'Profil penelitian diperbarui dari detail user: ' . $user['email']
        );

        return redirect()
            ->to(base_url('admin/admin-users/' . $userId))
            ->with('success', 'Profil penelitian berhasil disimpan.');
    }

    private function profileValidationRules(): array
    {
        $rules = [
            'nama_penelitian'  => 'required|max_length[255]',
            'nama_peneliti'    => 'permit_empty|max_length[150]',
            'nim'              => 'permit_empty|max_length[50]',
            'institusi'        => 'permit_empty|max_length[150]',
            'program_studi'    => 'permit_empty|max_length[150]',
            'tahun_penelitian' => 'permit_empty|max_length[20]',
        ];

        $pdf = $this->request->getFile('ringkasan_penelitian_pdf');
        if ($pdf && $pdf->getError() !== UPLOAD_ERR_NO_FILE) {
            $rules['ringkasan_penelitian_pdf'] = 'uploaded[ringkasan_penelitian_pdf]|max_size[ringkasan_penelitian_pdf,10240]|ext_in[ringkasan_penelitian_pdf,pdf]|mime_in[ringkasan_penelitian_pdf,application/pdf,application/x-pdf]';
        }

        return $rules;
    }

    private function saveProfileFromRequest(): void
    {
        $pdf = $this->request->getFile('ringkasan_penelitian_pdf');
        $fields = [
            'nama_penelitian',
            'nama_peneliti',
            'nim',
            'institusi',
            'program_studi',
            'tahun_penelitian',
        ];

        foreach ($fields as $field) {
            $this->settingModel->setValue(
                $field,
                trim((string) $this->request->getPost($field)),
                'profile'
            );
        }

        if ($pdf && $pdf->isValid() && !$pdf->hasMoved()) {
            $targetDir = FCPATH . 'uploads/settings';
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0775, true);
            }

            $oldPath  = (string) ($this->settingModel->getValue('ringkasan_penelitian_pdf') ?? '');
            $fileName = 'ringkasan-penelitian-' . date('YmdHis') . '.pdf';
            $pdf->move($targetDir, $fileName);

            $newPath = 'uploads/settings/' . $fileName;
            $this->settingModel->setValue('ringkasan_penelitian_pdf', $newPath, 'profile');

            if ($oldPath !== '' && str_starts_with($oldPath, 'uploads/settings/')) {
                $oldFullPath = FCPATH . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $oldPath);
                if (is_file($oldFullPath)) {
                    unlink($oldFullPath);
                }
            }
        }
    }

    private function normalizeRole(string $role): string
    {
        $role = strtolower(trim($role));

        return in_array($role, ['superadmin', 'admin'], true) ? $role : 'admin';
    }

    private function countActiveSuperadmins(): int
    {
        return (int) $this->userModel
            ->where('role', 'superadmin')
            ->where('status', 'aktif')
            ->countAllResults();
    }
}
