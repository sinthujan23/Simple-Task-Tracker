<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class TaskController extends Controller
{
    // Create a new task
    public function create(Request $request)
    {
        // Input validation
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Using prepared statements via Laravel's query builder
            $task = Task::create([
                'title' => $request->title,
                'is_completed' => false
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Task created successfully',
                'data' => $task
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to create task',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Get all tasks
    public function list()
    {
        try {
            // Using prepared statement for security
            $tasks = DB::select("SELECT * FROM tasks ORDER BY created_at DESC");
            
            // Convert to Eloquent models for consistency
            $taskModels = Task::hydrate($tasks);

            return response()->json([
                'status' => true,
                'message' => 'Tasks retrieved successfully',
                'data' => $taskModels
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to retrieve tasks',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Mark task as completed
    public function complete($id)
    {
        try {
            // Find task using prepared statement
            $task = DB::selectOne("SELECT * FROM tasks WHERE id = ?", [$id]);

            if (!$task) {
                return response()->json([
                    'status' => false,
                    'message' => 'Task not found'
                ], 404);
            }

            // Update using prepared statement
            DB::update("UPDATE tasks SET is_completed = 1, updated_at = ? WHERE id = ?", [
                now(),
                $id
            ]);

            // Get updated task
            $updatedTask = DB::selectOne("SELECT * FROM tasks WHERE id = ?", [$id]);

            return response()->json([
                'status' => true,
                'message' => 'Task marked as completed',
                'data' => $updatedTask
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to update task',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}