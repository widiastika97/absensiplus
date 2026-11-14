<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UpdateFotoProfilController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = Auth::guard('karyawan')->user();

        $request->validate([
            'foto_profil' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:3072'],
        ], [
            'foto_profil.max' => 'Ukuran foto profil maksimal 3 MB.',
        ]);

        $file = $request->file('foto_profil');
        $fileName = 'avatar_'.$user->id.'_'.time().'.'.strtolower($file->getClientOriginalExtension());

        $file->storeAs('avatars', $fileName, 'public');

        // Hapus avatar lama (bukan default)
        if ($user->foto_profil && $user->foto_profil !== 'default-avatar.png') {
            Storage::disk('public')->delete('avatars/'.$user->foto_profil);
        }

        $user->update(['foto_profil' => $fileName]);

        return response()->json([
            'status' => 'success',
            'message' => 'Foto profil berhasil diperbarui!',
            'foto_url' => asset('storage/avatars/'.$fileName),
        ]);
    }
}
