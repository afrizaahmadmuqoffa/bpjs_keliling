<?php

namespace App\Http\Controllers;

use App\Models\ParticipantModel;
use App\Models\RegionModel;
use App\Models\ServiceModel;
use App\Models\SegmentModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Support\Facades\DB;

class ParticipantController extends Controller
{

    public function index()
    {
        return view('participants.index');
    }

    public function create()
    {
        return view('participants.create');
    }

    public function edit($id)
    {
        $participant = ParticipantModel::with('region')->findOrFail($id);

        // PIC hanya bisa edit data miliknya sendiri
        if (Auth::user()->role === 'pic' && $participant->created_by !== Auth::id()) {
            abort(403, 'Tidak punya akses terhadap data ini');
        }

        return view('participants.edit', [
            'participant' => $participant,
        ]);
    }

    public function downloadTemplate()
    {
        $path = storage_path('app/template_import_excel_bpjs_keliling.xlsx');

        abort_if(!file_exists($path), 404);

        return response()->download($path, 'template_import_excel_bpjs_keliling.xlsx');
    }
}
