<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use Illuminate\Support\Facades\Auth;
use App\Helpers\LogHelper;

class MarketingCustomerController extends Controller
{

    public function index(Request $request)
    {
        if (!Auth::check()) {
            return redirect('/')->with('error', 'Silakan login terlebih dahulu!');
        }

        $query = Customer::query();

        if ($request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $customers = $query->latest()->paginate(10)->withQueryString();

        return view('marketing.customers', compact('customers'));
    }

    public function update(Request $request, $id)
    {
        if (!Auth::check()) {
            return redirect('/')->with('error', 'Silakan login terlebih dahulu!');
        }

        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'phone' => 'required|regex:/^0[0-9]{10,}$/',
            'address' => 'required|string',
            'instagram' => 'nullable|string',
            'organization' => 'required',
            'agency' => 'nullable|string'
        ]);

        $customer = Customer::findOrFail($id);

        $oldData = $customer->toArray();

        $customer->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'instagram' => $request->instagram,
            'organization' => $request->organization,
            'agency' => $request->agency
        ]);

        $newData = $customer->fresh()->toArray();

        $fieldLabels = [
            'name' => 'Nama',
            'email' => 'Email',
            'phone' => 'No. Telp',
            'address' => 'Alamat',
            'instagram' => 'Instagram',
            'organization' => 'Organisasi',
            'agency' => 'Instansi/Agensi'
        ];

        $messages = [];

        foreach ($fieldLabels as $field => $label) {
            if (($oldData[$field] ?? null) != ($newData[$field] ?? null)) {
                $messages[] = $label .
                    ' diubah dari "' . ($oldData[$field] ?? '-') .
                    '" menjadi "' . ($newData[$field] ?? '-') . '"';
            }
        }

        if (!empty($messages)) {
            LogHelper::add(
                'warning',
                'Update Customer (#' . $customer->id . ')',
                implode("\n", $messages)
            );
        }

        return redirect()
            ->back()
            ->with('success', 'Data customer berhasil diupdate');
    }

    /*
    ============================
    SIMPAN RATING
    ============================
    */

    public function rate(Request $request, $id)
    {
        $request->validate([
            'rating' => 'required|numeric|min:1|max:5',
            'review' => 'nullable|string'
        ]);

        $customer = Customer::findOrFail($id);

        $customer->update([
            'rating' => $request->rating,
            'review' => $request->review
        ]);

        LogHelper::add(
            'info',
            'Rate Customer (#' . $customer->id . ')',
            'Nama: ' . $customer->name .
            ', Rating: ' . $request->rating
        );

        return redirect()
            ->back()
            ->with('success', 'Rating customer berhasil disimpan');
    }
}