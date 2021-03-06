<?php

namespace App\Http\Controllers\Pages;

use App\Models\Uji;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Support\Facades\GlobalAuth;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class PenilaianPageController extends Controller
{
    public function __construct()
    {
        $this->middleware([
            'auth:user'
        ]);
    }

    /**
     * Menampilkan daftar uji yang berkaitan dengan asesor tertentu
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = $this->filterStatus($request)
            ->when($request->q, function ($query) use ($request) {
                $query->where('nim', $request->q);
                $query->orWhereHas('getMahasiswa', function ($query) use ($request) {
                    $query->where('nama', 'ILIKE', "%{$request->q}%");
                });
            })->when((int) $request->event > 0, function ($query) use ($request) {
                $query->where('event_id', $request->event);
            })->distinct()->get(['id'])->pluck(['id']);

        $data = Uji::whereIn('id', $data)
            ->paginate(10)
            ->appends($request->only(['q', 'status', 'skema']));
            
        return view('menu.penilaian.index', [
            'data' => $data,
            'q' => $request->q
        ]);
    }

    /**
     * Melakukan filter uji terhadap status tertentu
     *
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    private function filterStatus(Request $request)
    {
        $data = GlobalAuth::user()->getUjiAsAsesor()->distinct();

        if ($request->status == 0 || $request->status == null) {
            // dalam proses penilaian, artinya, uji yang belum asesmen diri oleh asesor
            // atau belum dinilai, atau belum dikonfirmasi
            $data->where(function ($query) {
                $query->where(function ($query) {
                    $query->filterByStatus(Uji::MENGISI_ASESMEN_DIRI);
                })->orWhere(function ($query) {
                    $query->filterByStatus(Uji::LULUS_ASESMEN_DIRI);
                })->orWhere(function ($query) {
                    $query->filterByStatus(Uji::PROSES_PENILAIAN);
                })->orWhere(function ($query) {
                    $query->filterByStatus(Uji::TIDAK_LULUS_ASESMEN_DIRI)
                        ->where('konfirmasi_asesmen_diri', false);
                });
            })->where('tidak_melanjutkan_asesmen', false);
        } else if ($request->status == 1) {
            // Lulus Sertifikasi maupun hingga memiliki sertifikat
            $data->where(function ($query) {
                $query->where(function ($query) {
                    $query->filterByStatus(Uji::LULUS);
                })->orWhere(function ($query) {
                    $query->filterByStatus(Uji::MEMILIKI_SERTIFIKAT);
                });
            });
        } else if ($request->status == 2) {
            // tidak lulus sertifikasi
            $data->where(function ($query) {
                $query->where(function ($query) {
                    $query->filterByStatus(Uji::TIDAK_LULUS);
                });
            });
        } else if ($request->status == 3) {
            // tidak lulus asesmen diri
            $data->where(function ($query) {
                $query->where(function ($query) {
                    $query->filterByStatus(Uji::TIDAK_LULUS_ASESMEN_DIRI);
                });
            });
        }

        return $data;
    }

    /**
     * Menampilkan halaman penilaian untuk uji tertentu
     *
     * @param \App\Models\Uji $uji
     * @return \Illuminate\Http\Response
     */
    public function nilai(Uji $uji)
    {
        GlobalAuth::authorize('penilaian', $uji);

        return view('menu.penilaian.nilai', [
            'uji' => $uji
        ]);
    }

    /**
     * Mengisi form FR AI 02 oleh asesor
     *
     * @param \App\Models\Uji $uji
     * @return \Illuminate\Http\Response
     */
    public function isiFRAI02(Uji $uji)
    {
        return view('menu.penilaian.fr_ai_02', [
            'uji'       => $uji,
            'mahasiswa' => $uji->getMahasiswa(false),
            'skema'     => $uji->getSkema(false),
            'isian'     => $uji->getJawabanObservasi()
        ]);
    }


    public function FRAI04(Uji $uji)
    {
        $portofolios = $uji->getPortofolio();
        $types = ['Valid', 'Memadai', 'Asli', 'Terkini'];
        $frai04 = $uji->isHelperHasKey('FR.AI.04') ? $uji->helper['FR.AI.04'] : [];
        return view('menu.penilaian.fr_ai_04', [
            'portofolios' => $portofolios,
            'uji' => $uji,
            'types' => $types,
            'frai04' => $frai04
        ]);
    }

    public function FRAI05(Uji $uji)
    {
        $form = $uji->isHelperHasKey('FR.AI.05') ? $uji->helper['FR.AI.05'] : [];
        $n_form = $uji->isHelperHasKey('FR.AI.05') ? count($uji->helper['FR.AI.05']['unit']) : 0;
        return view('menu.penilaian.fr_ai_05', [
            'uji' => $uji,
            'form' => $form,
            'n_form' => $n_form
        ]);
    }
}
