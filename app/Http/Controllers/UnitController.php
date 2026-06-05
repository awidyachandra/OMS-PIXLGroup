<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Unit;
use Illuminate\Support\Facades\Auth;
use App\Helpers\LogHelper;

class UnitController extends Controller
{

    public function index(Request $request)
    {
        if (!Auth::check()) {
            return redirect('/')->with('error', 'Silakan login terlebih dahulu!');
        }

        $query = Unit::query();

        if ($request->backup == '1') {
            $query->where('is_backup', 1);
        } else {
            $query->where('is_backup', 0);
        }

        // SEARCH
        if ($request->search) {
            $query->where('nama_unit', 'like', '%' . $request->search . '%');
        }

        if ($request->kategori) {
            $query->where('kategori', $request->kategori);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $units = $query->paginate(10)->withQueryString();

        return view('storage.equipment', compact('units'));
    }

    public function dashboard()
    {
        if (!Auth::check()) {
            return redirect('/')->with('error', 'Silakan login terlebih dahulu!');
        }

        return view('storage.dashboard');
    }

    // ======================
    // SIMPAN DATA
    // ======================
    public function store(Request $request)
    {
        $request->validate([
            'nama_unit' => 'required',
            'harga_sewa' => 'required|numeric',
            'kategori' => 'required',
            'status' => 'required',
            'is_backup' => 'nullable|boolean'
        ]);

        // ======================
        // PREFIX BERDASARKAN KATEGORI
        // ======================
        $prefixMap = [
            'HT' => 'ht',
            'Photobooth' => 'pb',
            'SMOKE STAGE LED' => 'sm',
            'HDTV SPLITTER' => 'hs',
            'KABEL HDMI' => 'kh'
        ];

        $prefix = $prefixMap[$request->kategori];

        $lastUnit = Unit::where('kode_unit', 'like', $prefix . '%')
            ->orderBy('kode_unit', 'desc')
            ->first();

        if ($lastUnit) {
            $lastNumber = (int) substr($lastUnit->kode_unit, 2);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        $kodeBaru = $prefix . str_pad($newNumber, 2, '0', STR_PAD_LEFT);

        // ======================
        // SIMPAN
        // ======================
        $unit = Unit::create([
            'kode_unit' => $kodeBaru,
            'nama_unit' => $request->nama_unit,
            'kategori' => $request->kategori,
            'harga_sewa' => $request->harga_sewa,
            'status' => $request->status,
            'is_backup' => $request->has('is_backup') ? 1 : 0
        ]);

        LogHelper::add(
            'info',
            'Tambah Unit',
            'Kode: ' . $unit->kode_unit .
            ', Nama: ' . $unit->nama_unit .
            ', Kategori: ' . $unit->kategori .
            ', Harga: ' . $unit->harga_sewa .
            ', Status: ' . $unit->status .
            ', Backup: ' . ($unit->is_backup ? 'Ya' : 'Tidak')
        );

        return back()->with('success', 'Unit berhasil ditambahkan');
    }

    // ======================
    // UPDATE
    // ======================
    public function update(Request $request)
    {
        $unit = Unit::find($request->id);

        if (!$unit) {
            return back()->with('error', 'Unit tidak ditemukan');
        }

        // ======================
        // SIMPAN DATA LAMA
        // ======================
        $oldData = $unit->toArray();

        // ======================
        // UPDATE DATA
        // ======================
        $unit->update([
            'nama_unit' => $request->nama_unit,
            'harga_sewa' => $request->harga_sewa,
            'status' => $request->status,
            'is_backup' => $request->has('is_backup') ? 1 : 0
        ]);

        // ======================
        // AMBIL DATA BARU
        // ======================
        $newData = $unit->fresh()->toArray();

        // ======================
        // LABEL FIELD
        // ======================
        $fieldLabels = [
            'nama_unit' => 'Nama Unit',
            'harga_sewa' => 'Harga Sewa',
            'status' => 'Status',
            'kategori' => 'Kategori',
            'is_backup' => 'Backup Unit'
        ];

        // ======================
        // CEK PERUBAHAN
        // ======================
        $messages = [];

        foreach ($newData as $key => $value) {
            if (isset($oldData[$key]) && $oldData[$key] != $value) {

                $label = $fieldLabels[$key] ?? $key;

                if ($key == 'is_backup') {
                    $oldValue = $oldData[$key] ? 'Ya' : 'Tidak';
                    $newValue = $value ? 'Ya' : 'Tidak';
                } else {
                    $oldValue = $oldData[$key];
                    $newValue = $value;
                }

                $messages[] = $label .
                    ' diubah dari "' . $oldValue .
                    '" menjadi "' . $newValue . '"';
            }
        }

        // ======================
        // SIMPAN LOG
        // ======================
        if (!empty($messages)) {

            $context = implode("\n", $messages);

            LogHelper::add(
                'warning',
                'Update Unit (' . $unit->kode_unit . ')',
                $context
            );
        }

        return back()->with('success', 'Unit berhasil diupdate');
    }

    // ======================
    // DELETE
    // ======================
    public function delete($id)
    {
        $unit = Unit::find($id);

        if (!$unit) {
            return back()->with('error', 'Unit tidak ditemukan');
        }

        $kode = $unit->kode_unit;
        $nama = $unit->nama_unit;

        $unit->delete();

        LogHelper::add(
            'warning',
            'Delete Unit',
            'Kode: ' . $kode . ', Nama: ' . $nama
        );

        return back()->with('success', 'Unit berhasil dihapus');
    }

    public function getKode($kategori)
    {
        $prefixMap = [
            'HT' => 'ht',
            'Photobooth' => 'pb',
            'SMOKE STAGE LED' => 'sm',
            'HDTV SPLITTER' => 'hs',
            'KABEL HDMI' => 'kh',
            'EARPHONE' => 'ep'
        ];

        $prefix = $prefixMap[$kategori] ?? 'xx';

        $lastUnit = Unit::where('kode_unit', 'like', $prefix . '%')
            ->orderBy('kode_unit', 'desc')
            ->first();

        if ($lastUnit) {
            $lastNumber = (int) substr($lastUnit->kode_unit, 2);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        $kodeBaru = $prefix . str_pad($newNumber, 2, '0', STR_PAD_LEFT);

        return response()->json([
            'kode' => $kodeBaru
        ]);
    }
}