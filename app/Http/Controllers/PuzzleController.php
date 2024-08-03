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

    public function play()
    {
        $puzzles = [
            ['id' => 1, 'title' => 'Puzzle 1', 'expected_answer' => 'Answer 1'],
            ['id' => 2, 'title' => 'Puzzle 2', 'expected_answer' => 'Answer 2'],
            ['id' => 3, 'title' => 'Puzzle 3', 'expected_answer' => 'Answer 3'],
            ['id' => 4, 'title' => 'Puzzle 4', 'expected_answer' => 'Answer 4'],
            ['id' => 5, 'title' => 'Puzzle 5', 'expected_answer' => 'Answer 5'],
            ['id' => 6, 'title' => 'Puzzle 6', 'expected_answer' => 'Answer 6'],
            ['id' => 7, 'title' => 'Puzzle 7', 'expected_answer' => 'Answer 7'],
            ['id' => 8, 'title' => 'Puzzle 8', 'expected_answer' => 'Answer 8'],
            ['id' => 9, 'title' => 'Puzzle 9', 'expected_answer' => 'Answer 9'],
            ['id' => 10, 'title' => 'Puzzle 10', 'expected_answer' => 'Answer 10'],
            ['id' => 11, 'title' => 'Puzzle 11', 'expected_answer' => 'Answer 11'],
        ];
        // $levels = 38;
        $levels = count($puzzles);
        $unlocked = 2;
        return view('pages.play-puzzle.index', compact('puzzles', 'levels', 'unlocked'));
    }

    public function detail()
    {
        return view('pages.play-puzzle._detail');
    }
}
