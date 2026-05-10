<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\InstrumentModel;
use App\Models\ProductInstrumentModel;
use App\Models\ResearchProductModel;
use App\Models\SettingModel;

class Products extends BaseController
{
    protected ResearchProductModel $productModel;
    protected InstrumentModel $instrumentModel;
    protected ProductInstrumentModel $productInstrumentModel;
    protected SettingModel $settingModel;

    public function __construct()
    {
        $this->productModel           = new ResearchProductModel();
        $this->instrumentModel        = new InstrumentModel();
        $this->productInstrumentModel = new ProductInstrumentModel();
        $this->settingModel           = new SettingModel();
    }

    public function index()
    {
        $keyword = trim((string) $this->request->getGet('keyword'));
        $perPage = config('Pager')->perPage;

        $query = $this->productModel;

        if ($keyword !== '') {
            $query = $query
                ->groupStart()
                ->like('kode', $keyword)
                ->orLike('nama_produk', $keyword)
                ->orLike('jenis_produk', $keyword)
                ->orLike('status', $keyword)
                ->groupEnd();
        }

        $data = [
            'title'    => 'Produk Penelitian',
            'keyword'  => $keyword,
            'products' => $query->orderBy('id', 'DESC')->paginate($perPage, 'products'),
            'pager'    => $this->productModel->pager,
            'pagerGroup' => 'products',
        ];

        return view('admin/products/index', $data);
    }

    public function new()
    {
        $data = [
            'title'       => 'Tambah Produk Penelitian',
            'product'     => null,
            'jenisOptions' => $this->getJenisProdukOptions(),
            'instruments' => $this->getAvailableInstruments(),
            'selectedInstruments' => [],
            'action'      => base_url('admin/products'),
            'method'      => 'post',
        ];

        return view('admin/products/form', $data);
    }

    public function create()
    {
        $rules = [
            'kode'         => 'required|min_length[2]|max_length[50]|is_unique[research_products.kode]',
            'nama_produk'  => 'required|min_length[3]|max_length[255]',
            'jenis_produk' => 'required',
            'status'       => 'required',
            'file_produk'  => 'permit_empty|max_size[file_produk,10240]|ext_in[file_produk,pdf,doc,docx,xls,xlsx,ppt,pptx,zip]',
        ];

        if (!$this->validate($rules)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $fileName = $this->uploadProductFile();

        $productId = $this->productModel->insert([
            'kode'         => trim((string) $this->request->getPost('kode')),
            'nama_produk'  => trim((string) $this->request->getPost('nama_produk')),
            'jenis_produk' => trim((string) $this->request->getPost('jenis_produk')),
            'deskripsi'    => trim((string) $this->request->getPost('deskripsi')),
            'file_produk'  => $fileName,
            'link_produk'  => trim((string) $this->request->getPost('link_produk')),
            'status'       => trim((string) $this->request->getPost('status')),
        ]);

        $this->saveProductInstruments((int) $productId);

        return redirect()
            ->to(base_url('admin/products'))
            ->with('success', 'Produk penelitian berhasil ditambahkan.');
    }

    public function show($id = null)
    {
        $product = $this->productModel->find($id);

        if (!$product) {
            return redirect()
                ->to(base_url('admin/products'))
                ->with('error', 'Data produk tidak ditemukan.');
        }

        $data = [
            'title'              => 'Detail Produk Penelitian',
            'product'            => $product,
            'productInstruments' => $this->productInstrumentModel->getByProduct((int) $id),
        ];

        return view('admin/products/show', $data);
    }

    public function download($id = null)
    {
        $product = $this->productModel->find($id);

        if (!$product || empty($product['file_produk'])) {
            return redirect()
                ->to(base_url('admin/products'))
                ->with('error', 'File produk tidak ditemukan.');
        }

        $filePath = WRITEPATH . 'uploads/products/' . $product['file_produk'];

        if (!is_file($filePath)) {
            return redirect()
                ->to(base_url('admin/products/' . $id))
                ->with('error', 'File produk tidak tersedia di server.');
        }

        return $this->response->download($filePath, null);
    }

    public function edit($id = null)
    {
        $product = $this->productModel->find($id);

        if (!$product) {
            return redirect()
                ->to(base_url('admin/products'))
                ->with('error', 'Data produk tidak ditemukan.');
        }

        $selectedInstruments = $this->productInstrumentModel
            ->where('product_id', $id)
            ->findAll();

        $selectedIds = array_map(static function ($row) {
            return (int) $row['instrument_id'];
        }, $selectedInstruments);

        $data = [
            'title'       => 'Edit Produk Penelitian',
            'product'     => $product,
            'jenisOptions' => $this->getJenisProdukOptions(),
            'instruments' => $this->getAvailableInstruments(),
            'selectedInstruments' => $selectedIds,
            'action'      => base_url('admin/products/' . $id),
            'method'      => 'put',
        ];

        return view('admin/products/form', $data);
    }

    public function update($id = null)
    {
        $product = $this->productModel->find($id);

        if (!$product) {
            return redirect()
                ->to(base_url('admin/products'))
                ->with('error', 'Data produk tidak ditemukan.');
        }

        $rules = [
            'kode'         => 'required|min_length[2]|max_length[50]|is_unique[research_products.kode,id,' . $id . ']',
            'nama_produk'  => 'required|min_length[3]|max_length[255]',
            'jenis_produk' => 'required',
            'status'       => 'required',
            'file_produk'  => 'permit_empty|max_size[file_produk,10240]|ext_in[file_produk,pdf,doc,docx,xls,xlsx,ppt,pptx,zip]',
        ];

        if (!$this->validate($rules)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $existingFile = isset($product['file_produk']) ? (string) $product['file_produk'] : null;
        $fileName = $this->uploadProductFile($existingFile);

        $this->productModel->update($id, [
            'kode'         => trim((string) $this->request->getPost('kode')),
            'nama_produk'  => trim((string) $this->request->getPost('nama_produk')),
            'jenis_produk' => trim((string) $this->request->getPost('jenis_produk')),
            'deskripsi'    => trim((string) $this->request->getPost('deskripsi')),
            'file_produk'  => $fileName,
            'link_produk'  => trim((string) $this->request->getPost('link_produk')),
            'status'       => trim((string) $this->request->getPost('status')),
        ]);

        $this->saveProductInstruments((int) $id, true);

        return redirect()
            ->to(base_url('admin/products'))
            ->with('success', 'Produk penelitian berhasil diperbarui.');
    }

    public function delete($id = null)
    {
        $product = $this->productModel->find($id);

        if (!$product) {
            return redirect()
                ->to(base_url('admin/products'))
                ->with('error', 'Data produk tidak ditemukan.');
        }

        if (!empty($product['file_produk'])) {
            $filePath = WRITEPATH . 'uploads/products/' . $product['file_produk'];

            if (is_file($filePath)) {
                @unlink($filePath);
            }
        }

        $this->productModel->delete($id);

        return redirect()
            ->to(base_url('admin/products'))
            ->with('success', 'Produk penelitian berhasil dihapus.');
    }

    private function uploadProductFile(?string $oldFileName = null): ?string
    {
        $file = $this->request->getFile('file_produk');

        if (!$file || !$file->isValid()) {
            return $oldFileName;
        }

        if ($file->hasMoved()) {
            return $oldFileName;
        }

        $newName = $file->getRandomName();
        $file->move(WRITEPATH . 'uploads/products', $newName);

        if (!empty($oldFileName)) {
            $oldPath = WRITEPATH . 'uploads/products/' . $oldFileName;

            if (is_file($oldPath)) {
                @unlink($oldPath);
            }
        }

        return $newName;
    }

    private function saveProductInstruments(int $productId, bool $deleteOld = false): void
    {
        if ($deleteOld) {
            $this->productInstrumentModel
                ->where('product_id', $productId)
                ->delete();
        }

        $instrumentIds = $this->request->getPost('instrument_ids');

        if (empty($instrumentIds) || !is_array($instrumentIds)) {
            return;
        }

        foreach ($instrumentIds as $instrumentId) {
            $instrument = $this->instrumentModel->find((int) $instrumentId);

            if (!$instrument) {
                continue;
            }

            $this->productInstrumentModel->insert([
                'product_id'    => $productId,
                'instrument_id' => (int) $instrumentId,
                'keterangan'    => 'Instrumen validasi untuk produk penelitian.',
            ]);
        }
    }

    private function getAvailableInstruments(): array
    {
        return $this->instrumentModel
            ->whereIn('jenis', ['Validasi Produk', 'Validasi Instrumen'])
            ->orderBy('judul', 'ASC')
            ->findAll();
    }

    private function getJenisProdukOptions(): array
    {
        return $this->settingModel->getProductTypes([
            'Buku Model',
            'Buku Ajar',
            'Materi Ajar',
            'Panduan Pembelajaran',
            'E-Learning',
            'Rubrik',
            'Template Artikel',
            'Produk Lainnya',
        ]);
    }
}
