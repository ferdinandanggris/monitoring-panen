<?
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Session;
use Illuminate\Http\JsonResponse;

class SessionController extends Controller
{
    /**
     * Hapus Session beserta semua detail-nya.
     */
    public function destroy(Session $session): JsonResponse
    {
        // Jika belum menggunakan onDelete cascade di migration:
        // $session->details()->delete();

        $session->delete();

        return response()->json([
            'message' => 'Session dan semua detail-nya berhasil dihapus.'
        ], 200);
    }
}