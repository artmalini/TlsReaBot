@extends('backend.layouts.app')

@section('content')
    <div class="container">
        @if (Session::has('status'))
            <div class="alert alert-info">
                <span>{{Session::get('status')}}</span>
            </div>
        @endif
        <form action="{{route('admin.setting.store')}}" method="POST">
            {{csrf_field()}}
            <div class="form-group">
                <label>URL TlsReaBot</label>
                <div class="input-group-btn">
                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Action <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu">
                        <li><a href="#" onclick="document.getElementById('url_call_bot').value='{{url('')}}'">Вставить url</a></li>
                        <li><a href="#" onclick="event.preventDefault(); document.getElementById('setwebhook').submit();">Отправить url</a></li>
                        <li><a href="#" onclick="event.preventDefault(); document.getElementById('getwebhookinfo').submit();">Получить информацию</a></li>
                    </ul>
                </div>
                <input type="url" class="form-control" id="url_call_bot" name="url_call_bot" value="{{$url_call_bot or ''}}">
            </div>
            <div class="form-group">
                <label>Дополнительная информация</label>
                <input type="text" class="form-control" name="exp_info" value="{{$exp_info or ''}}">
            </div>
            <button class="btn btn-primary" type="submit">Отправить</button>
        </form>

         <form id="setwebhook" action="{{route('admin.setting.setwebhook')}}" method="post" style="display: none;">
             {{csrf_field()}}
             <input type="hidden" name="url" value="{{$url_call_bot or ''}}">
         </form>

            <form id="getwebhookinfo" action="{{route('admin.setting.getwebhookinfo')}}" method="post" style="display: none;">
                {{csrf_field()}}
            </form>
    </div>
    @endsection