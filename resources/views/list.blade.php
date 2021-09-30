@extends('layout')

@section('content')
    <div class="container p-5">
        <h1>{{__('List all projects')}}</h1>

        <div class="card">
            <div class="card-body">

                @foreach ($projects as $item)
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label"> {{ $item->project_name ?? ''}}</label>
                        <div class="col-sm-8">
                            <a href="{{ route('index.show', ['hash' => $item->hash])}}"> {{ $item->hash ?? ""}}</a>
                        </div>
                    </div>
                @endforeach

            </div>
        </div>

    </div>

@endsection
