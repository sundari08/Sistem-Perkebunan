<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Kreait\Firebase\Contract\Firestore;

class TestPanenController extends Controller
{
    protected $collection;

    public function __construct(Firestore $firestore)
    {
        $this->collection = $firestore->database()->collection('hasil_panen');
    }

    public function simpan(Request $request)
    {
        $data = [
            'tgl' => $request->tgl ?? date('Y-m-d'),
            'estate' => $request->estate ?? 'Test Estate',
            'divisi' => $request->divisi ?? 'Test Divisi',
            'blok' => $request->blok ?? 'Test Blok',
            'mandor' => $request->mandor ?? 'Test Mandor',
            'kerani' => $request->kerani ?? 'Test Kerani',
            'tph' => $request->tph ?? 1,
            'pemanen' => $request->pemanen ?? 'Test Pemanen',
            'janjang' => $request->janjang ?? 100,
            'matang' => $request->matang ?? 80,
            'mentah' => $request->mentah ?? 10,
            'kurangmatang' => $request->kurangmatang ?? 5,
            'lewatmatang' => $request->lewatmatang ?? 3,
            'partenorcarpi' => $request->partenorcarpi ?? 1,
            'buahbatu' => $request->buahbatu ?? 1,
            'created_at' => now()->toISOString(),
        ];

        $document = $this->collection->add($data);

        return response()->json([
            'success' => true,
            'message' => 'Data tersimpan!',
            'id' => $document->id()
        ]);
    }

    public function semua()
    {
        $documents = $this->collection->documents();
        $data = [];

        foreach ($documents as $document) {
            if ($document->exists()) {
                $data[] = [
                    'id' => $document->id(),
                    ...$document->data()
                ];
            }
        }

        return response()->json($data);
    }
}