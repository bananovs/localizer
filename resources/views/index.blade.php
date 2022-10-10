@extends('layout')

@section('content')
    <div class="container p-5">
        <h1>{{__('Simple Localizer')}}</h1>
        <div class="card">
            <div class="card-body">

                <form id="form" method="POST" action="{{ route('index.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">{{__('Project name')}}</label>
                        <div class="col-sm-8">
                            <input type="text" name="project_name" class="form-control" placeholder="Project name">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">{{ __('Language trans') }} </label>
                        <div class="col-sm-8">
                            <select class="form-control" name="lang">
                                @foreach ($langs as $k => $v)
                                    <option value="{{ $k }}">{{ $v }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">{{__('Localize file json if exists')}}</label>
                        <div class="col-sm-8">
                            <div class="custom-file">
                                <label class="btn btn-outline-primary" for="my-file-selector">
                                    <input id="my-file-selector" type="file" name="file" style="display:none" onchange="$('#upload-file-info').text(this.files[0].name)">
                                    {{__('Upload file')}}
                                </label>
                                <span class='label label-info' id="upload-file-info"></span>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-sm-8 offset-4">
                            <button type="submit" class="btn btn-primary">{{__('Let\'s go!')}}</button>
                        </div>
                    </div>
                </form>

            </div>
        </div>

    </div>

@endsection
