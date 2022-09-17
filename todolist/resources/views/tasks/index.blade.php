@extends('layout')

@section('content')

    {{-- flashメッセージを表示 --}}
    @if (session('status'))
        <x-ui.flash_message message="{{ session('status') }}"></x-ui.flash_message>
    @endif

    <div class="container">
        <div class="row">
            <div class="col col-md-4">
                <nav class="panel panel-default">
                    <div class="panel-heading">フォルダ</div>
                    <div class="panel-body">
                        <a href="{{ route('folders.create') }}" class="btn btn-default btn-block">
                        フォルダを追加する
                        </a>
                    </div>
                    <div class="list-group">
                        @foreach($folders as $folder)
                        <div class="list-group-el">
                            <a href="{{ route('tasks.index', ['folder' => $folder->id]) }}" class="list-group-item {{ $current_folder_id === $folder->id ? 'active' : '' }}">
                                {{ $folder->title }}
                            </a>
                            <form method="POST" action="{{ route('folder.remove', ['folder' => $folder->id]) }}">
                                @csrf
                                @method('delete')

                                <button onclick="return confirm('本当に削除してもよろしいですか？');">削除</button>
                            </form>
                        </div>
                        @endforeach
                    </div>
                </nav>
            </div>


            <div class="column col-md-8">
                <div class="panel panel-default">
                    <div class="panel-heading">タスク</div>
                    <div class="panel-body">
                        <div class="text-right">
                            <a href="{{ route('tasks.create', ['folder' => $current_folder_id]) }}" class="btn btn-default btn-block">
                                タスクを追加する
                            </a>
                        </div>
                    </div>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>タイトル</th>
                                <th>状態</th>
                                <th>期限</th>
                                <th></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($tasks as $task)
                            <tr>
                                <td>{{ $task->title }}</td>
                                <td>
                                    <span class="label {{ $task->status_class }}">{{ $task->status_label }}</span>
                                </td>
                                <td>{{ $task->formatted_due_date }}</td>
                                <td><a href="{{ route('tasks.edit', ['folder' => $task->folder_id, 'task' => $task->id]) }}">編集</a></td>
                                <td>
                                    <form method="POST" action="{{ route('task.remove', ['folder' => $task->folder_id, 'task' => $task->id]) }}">
                                        @csrf
                                        @method('delete')
                                        <button onclick="return confirm('本当に削除してもよろしいですか？');">削除</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
