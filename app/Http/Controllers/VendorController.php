<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use Illuminate\Http\Request;

class VendorController extends Controller
{
    // Tampilkan semua vendor
    public function index()
    {
        return response()->json(Vendor::all());
    }

    // Simpan vendor baru
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'created_by' => 'required|exists:users,id',
        ]);

        $vendor = Vendor::create($validated);
      return response($vendor, 201)
         ->header('Content-Type', 'application/json');

    }

    // Tampilkan vendor berdasarkan ID
    public function show($id)
    {
        $vendor = Vendor::findOrFail($id);
        return response()->json($vendor);
    }

    // Update vendor
    public function update(Request $request, $id)
    {
        $vendor = Vendor::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'phone' => 'sometimes|string|max:20',
            'address' => 'sometimes|string',
            'created_by' => 'sometimes|exists:users,id',
        ]);

        $vendor->update($validated);
        return response()->json($vendor);
    }

    // Hapus vendor
    public function destroy($id)
    {
        Vendor::destroy($id);
        return response()->json(null, 204);
    }
}
