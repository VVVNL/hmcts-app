<?php

namespace App\Http\Controllers;

use App\Models\Status;
use App\Models\Task;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TaskController extends Controller
{
    /**
     * Show the form for creating a new task.
     */
    public function create()
    {
        return Inertia::render('Tasks/Create');
    }

    /**
     * Store a newly created task in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due' => 'required|date_format:Y-m-d\\TH:i|after_or_equal:today',
        ]);

        $task = new Task($validated);
        $task->status()->associate(Status::find(1));
        $task->user()->associate(auth()->user());

        $task->save();

        // return success message and task details
        return Inertia::render('Tasks/Create', [
            'success' => 'Task created successfully.',
            'task' => $task->refresh(),
        ]);
    }
}
