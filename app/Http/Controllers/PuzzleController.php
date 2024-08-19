<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Puzzle;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;

class PuzzleController extends Controller
{

    /**
     * Path to store puzzle pictures.
     */
    protected $path_picture_puzzle;

    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->path_picture_puzzle = config('dirpath.puzzle.pictures');
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Puzzle::select(['id', 'title', 'picture', 'expected_answer', 'index'])
            ->orderBy('index')
            ->get();
            return DataTables::of($data)->make(true);
        }

        return view('pages.puzzle-regeneration.index');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'expected_answer' => 'required',
            'picture' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error!',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $picture = $request->file('picture');
            $originalName = pathinfo($picture->getClientOriginalName(), PATHINFO_FILENAME);
            $picture_name = date('Y-m-d-H-i-s') . '_' . $originalName . '.' . $picture->extension();
            $picture->storeAs($this->path_picture_puzzle, $picture_name, 'public');

            $maxIndex = Puzzle::max('index');
            $index = $maxIndex ? $maxIndex + 1 : 1;

            $puzzle = Puzzle::create([
                'title' => $request->title,
                'expected_answer' => $request->expected_answer,
                'picture' => $picture_name,
                'index' => $index,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Puzzles Level created successfully!',
                'data' => $puzzle,
            ], 201);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong!',
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Puzzle $puzzle)
    {
        try {
            return response()->json([
                'status' => 'success',
                'data' => $puzzle,
            ], 200);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong!',
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Puzzle $puzzle)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'expected_answer' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error!',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            if ($request->hasFile('picture')) {
                deleteFile($this->path_picture_puzzle . '/' . $puzzle->getAttributes()['picture']);
                $picture = $request->file('picture');
                $originalName = pathinfo($picture->getClientOriginalName(), PATHINFO_FILENAME);
                $picture_name = date('Y-m-d-H-i-s') . '_' . $originalName . '.' . $picture->extension();
                $picture->storeAs($this->path_picture_puzzle, $picture_name, 'public');
                $puzzle->picture = $picture_name;
            }

            $puzzle->update([
                'title' => $request->title,
                'expected_answer' => $request->expected_answer,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Puzzles Level updated successfully!',
                'data' => $puzzle,
            ], 200);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong!',
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Puzzle $puzzle)
    {
        try {
            deleteFile($this->path_picture_puzzle . '/' . $puzzle->getAttributes()['picture']);
            $puzzle->delete();

            $this->reorderIndexes();

            return response()->json([
                'status' => 'success',
                'message' => 'Puzzles Level deleted successfully!',
            ], 200);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong!',
            ], 500);
        }
    }

    private function reorderIndexes()
    {
        $puzzles = Puzzle::orderBy('index')->get();
        $index = 1;

        foreach ($puzzles as $puzzle) {
            $puzzle->update(['index' => $index]);
            $index++;
        }
    }

    public function play()
    {
        $puzzles = Puzzle::select(['id', 'title', 'picture', 'expected_answer', 'index'])
            ->orderBy('index')
            ->get();
        $levels = count($puzzles);
        $userLevel = User::find(auth()->id());
        return view('pages.play-puzzle.index', compact('puzzles', 'levels', 'userLevel'));
    }

    public function detail(Request $request)
    {
        $puzzleId = $request->query('id');
        $puzzle = Puzzle::findOrFail($puzzleId);

        return view('pages.play-puzzle._detail', compact('puzzle'));
    }

    public function levelUp(Request $request, Puzzle $puzzle)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error!',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = User::find(auth()->id());

        if ($request->password == $puzzle->expected_answer) {
            $user->update([
                'puzzle_level' => $puzzle->index + 1,
            ]);
    
            return response()->json([
                'status' => 'success',
                'message' => 'Selamat! Anda berhasil menjawab dengan benar.',
                'puzzle_id' => $puzzle->id,
            ], 200);
        } else {
            $errorMessages = [
                'Jawaban Anda salah, silakan coba lagi.',
                'Maaf, Anda kurang beruntung.',
                'Coba lagi, ya!',
                'Hmm, jawaban Anda kurang tepat.',
            ];
    
            $randomErrorMessage = $errorMessages[array_rand($errorMessages)];
    
            return response()->json([
                'status' => 'error',
                'message' => $randomErrorMessage,
            ], 400);
        }
    }
}
