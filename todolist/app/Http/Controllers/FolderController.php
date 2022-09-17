<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\CreateFolder;
use App\Models\Folder;
use Illuminate\Support\Facades\Auth;

class FolderController extends Controller
{
    /**
     * フォルダ作成ページ表示
     * @return view
     *
     */
    public function showCreateForm()
    {
        return view('folders.create');
    }


    /**
     * フォルダ作成・登録
     * @param $request
     *
     * @return redirect
     */
    public function create(CreateFolder $request)
    {
        $folder = new Folder();

        $folder->title = $request->title;
        $folder->user_id = Auth::user()->id;

        Auth::user()->folders()->save($folder);

        return redirect()->route('tasks.index', [
            'folder' => $folder->id,
        ])
            ->with('status', 'フォルダを追加しました！');
    }
}
