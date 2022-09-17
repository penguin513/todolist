<?php

namespace App\Http\Controllers;

use App\Models\Folder;
use App\Models\Task;
use Illuminate\Http\Request;
use App\Http\Requests\CreateTask;
use App\Http\Requests\EditTask;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    /**
     * FolderIDとTaskIDの結びつきチェック関数
     */
    private function checkRelation(Folder $folder, Task $task)
    {
        if ($folder->id !== $task->folder_id) {
            abort(404);
        }
    }


    /**
     * Topページ表示
     * @param $folder
     *
     * @return view
     */
    public function index(Folder $folder)
    {

        $folders = Auth::user()->folders()->get();

        $tasks = $folder->tasks()->get();

        return view('tasks.index', [
            'folders' => $folders,
            'current_folder_id' => $folder->id,
            'tasks' => $tasks,
        ]);
    }


    /**
     * タスク追加ページ表示
     * @param $folder
     *
     * @return view
     */
    public function showCreateForm(Folder $folder, Task $task)
    {
        return view('tasks.create', [
            'folder_id' => $folder->id
        ]);
    }


    /**
     * タスク追加ページ登録
     * @param $request, $folder
     *
     * @return redirect
     */
    public function create(Folder $folder, CreateTask $request)
    {
        $task = new Task();
        $task->title = $request->title;
        $task->due_date = $request->due_date;

        $folder->tasks()->save($task);

        return redirect()->route('tasks.index', [
            'folder' => $folder->id,
        ])
            ->with('status', 'タスクを追加しました！');
    }


    /**
     * タスク編集ページ表示
     * @param $folder, $task
     *
     * @return view
     */
    public function showEditForm(Folder $folder, Task $task)
    {
        $this->checkRelation($folder, $task);

        return view('tasks/edit', [
            'task' => $task,
        ]);
    }


    /**
     * タスク編集ページ登録
     * @param $request, $folder, $task
     *
     * @return redirect
     */
    public function edit(EditTask $request, Folder $folder, Task $task)
    {
        $this->checkRelation($folder, $task);

        $task->title = $request->title;
        $task->status = $request->status;
        $task->due_date = $request->due_date;

        $task->save();

        return redirect()->route('tasks.index', [
            'folder' => $task->folder_id,
        ])
            ->with('status', 'タスクを編集しました！');
    }


    /**
     * フォルダ削除の実行
     * @param Folder $folder
     * @param Task $task
     * @return redirect
     */
    public function folder_remove(Folder $folder) {

        $user = Auth::user();
        $user_id = $user->id;

        try {
            Task::where('folder_id', $folder->id)->delete();
            Folder::find($folder->id)->delete();

            $first_data = Folder::where('user_id', $user_id)->first();

            return redirect()->route('tasks.index', [
                'folder' => $first_data->id,
            ])
                ->with('status', '該当のフォルダ及びその中のタスクを削除しました！');

        } catch (\Exception $ex) {
            logger($ex->getMessage());

            $first_data = Folder::where('user_id', $user_id)->first();

            return redirect()->route('tasks.index', [
                'folder' => $folder->id,
            ])
                ->withErrors($ex->getMessage());
        }
    }


    /**
     * タスク削除の実行
     * @param Folder $folder
     * @param Task $task
     * @return redirect
     */
    public function task_remove(Folder $folder, Task $task) {

        $this->checkRelation($folder, $task);

        try {
            Task::find($task->id)->delete();
            return redirect()->route('tasks.index',[
                'folder' => $task->folder_id,
            ])
                ->with('status', '該当のタスクを削除しました！');

        } catch (\Exception $ex) {
            logger($ex->getMessage());
            return redirect()->route('tasks.index',[
                'folder' => $task->folder_id,
            ])
                ->withErrors($ex->getMessage());
        }
    }

}
