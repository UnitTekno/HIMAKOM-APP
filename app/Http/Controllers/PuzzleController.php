<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Cabinet;
use App\Models\DBU;
use App\Models\Program;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class PuzzleController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Program::select('*')
                ->with('lead:id,name', 'dbu:id,name', 'participants:id,name', 'cabinet:id,name')
                ->when(!auth()->user()->hasRole('SUPER ADMIN'), function ($query) {
                    $query->where('programs.dbu_id', auth()->user()->dbu()->first()->id);
                });

            return DataTables::of($data)->make();
        }

        return view('pages.puzzle-regeneration.index', [
            'dbus' => DBU::all(['id', 'name']),
            'cabinets' => Cabinet::where('is_active', true)->get(['id', 'name']),
        ]);
    }

    public function store(Request $request)
    {

    }

    public function update(Request $request, $id)
    {

    }

    public function destroy($id)
    {
        
    }
}
