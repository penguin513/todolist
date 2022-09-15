<?php

namespace App\Http\Controllers;

use App\Models\Folder;
use App\Models\Task;
use Illuminate\Http\Request;
use App\Http\Requests\CreateTask;

class TaskController extends Controller
{
    /**
     * Topページ表示
     * @param int $id
     *
     * @return view
     */
    public function index(int $id) {
        $folders = Folder::all();

        $current_folder = Folder::find($id);

        $tasks = $current_folder->tasks()->get();

        return view('tasks.index', [
            'folders' => $folders,
            'current_folder_id' => $current_folder->id,
            'tasks' => $tasks,
        ]);
    }


    /**
     * タスク追加ページ表示
     * @param int $id
     *
     * @return view
     */
    public function showCreateForm(int $id)
    {
        return view('tasks.create', [
            'folder_id' => $id
        ]);
    }


    /**
     * タスク追加ページ表示
     * @param CreateTask $request, int $id
     *
     * @return redirect
     */
    public function create(int $id, CreateTask $request) {
        $current_folder = Folder::find($id);

        $task = new Task();
        $task->title = $request->title;
        $task->due_date = $request->due_date;

        $current_folder->tasks()->save($task);

        return redirect()->route('tasks.index', [
            'id' => $current_folder->id,
        ]);
    }
}
