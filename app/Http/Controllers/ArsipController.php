<?php

namespace App\Http\Controllers;

use App\Models\Arsip;
use Barryvdh\DomPDF\Facade\Pdf;

class ArsipController extends Controller
{
    public function print(Arsip $arsip)
    {
        $pdf = Pdf::loadView('arsip.print', [
            'arsip' => $arsip
        ])->setPaper('a4', 'portrait');

        return $pdf->stream("arsip-{$arsip->no_surat}.pdf");
    }
}
