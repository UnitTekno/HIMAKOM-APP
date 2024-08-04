<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Puzzle;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

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
            $data = Puzzle::all(['id','title','picture','expected_answer']);
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

            $puzzle = Puzzle::create([
                'title' => $request->title,
                'expected_answer' => $request->expected_answer,
                'picture' => $picture_name,
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
}
