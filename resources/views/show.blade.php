@extends('layout')

@section('content')
    <div class="container-fluid p-5">
        <div class="form-group row">
            <div class="col-sm-10">
                <h1>{{ __("Project name:")}} {{ $project->project_name ?? ''}} <br>{{ __("Lang:")}} {{ $project->localize->loc_name }}</h1>
            </div>
            <div class="col-sm-2">
               <a href="{{ route('index.deepl', $project->hash) }}" class="btn bg-primary" style="color:orange;">DEEPL API GO</a>
            </div>
        </div>

        <div class="card">
            <div class="card-header sticky-top bg-dark" style="color:white">
                <div class="form-group row">
                    <div class="col-sm-1">
                        №
                    </div>
                    <div class="col-sm-3">
                        {{ __("Origin dev words") ?? ""}}
                    </div>
                    <div class="col-sm-4">
                        {{ __("Example trans words") ?? ""}}
                    </div>
                    <div class="col-sm-4">
                        {{ __("New trans") ?? ""}}
                    </div>
                </div>
            </div>
            <div class="card-body">
                <form id="form" method="POST" action="{{ route('index.download', $project->hash) }}" enctype="multipart/form-data">
                @csrf
                @foreach ($project->localize->locItem as $item)
                <div class="item" id="{{ $item->id }}">
                    <div class="form-group row">
                        <div class="col-sm-1">
                            {{ $item->id }}
                        </div>
                        <div class="col-sm-3">
                            <textarea name="row[{{ $item->id }}][origin]" data-label="{{ $item->id }}:origin" class="form-control datarow" rows="3" >{{ $item->origin ?? ''}}</textarea>
                        </div>
                        <div class="col-sm-4">
                            <textarea name="row[{{ $item->id }}][trans]" data-label="{{ $item->id }}:trans" class="form-control datarow" rows="3">{{ $item->trans ?? ''}}</textarea>
                        </div>
                        <div class="col-sm-4">
                            <textarea name="row[{{ $item->id }}][new_trans]" data-label="{{ $item->id }}:new_trans" class="form-control datarow" rows="3">{{ $item->new_trans ?? ''}}</textarea>
                        </div>
                    </div>
                </div>
                <div class="addItem" id="addItem"></div>
                @endforeach
                    <div class="form-group row offset-11">
                        <div class="col-sm-11">
                            <a href="#" onclick="newRow()" class="btn btn-success" id="addRow"> + </a>
                            <a href="#" onclick="dellRow()" class="btn btn-danger" id="dellRow"> — </a>
                        </div>
                    </div>
                <div class="form-group row offset-4">
                    <div class="col-sm-8">
                        <button type="submit" class="btn btn-primary">{{__('Download: Origin dev + New trans .json')}}</button>
                    </div>
                </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@push('js')
<script>
    $(document).ready(function() {
        $('.datarow').on('change', function() {
            var text = $(this).val();
            var label = $(this).data('label');
            let token   = $('meta[name="csrf-token"]').attr('content');

            $.ajax({
                url: "{{ route('index.store.item', ['hash' => $project->hash])}}",
                type: 'POST',
                data: {
                    _token: token,
                    label: label,
                    text: text
                },
                success: function(response) {
                    console.log(response);
                }
            });

        });
    });

    function newRow() {
        let token   = $('meta[name="csrf-token"]').attr('content');

        $.ajax({
            url: "{{ route('index.store.item.add', ['hash' => $project->hash])}}",
            type: 'POST',
            data: {
                _token: token,
                data: 1,
            },
            success: function(response) {
                console.log(response);
                $('.addItem').append('<div class="item" id="' + response + '"><div class="form-group row"><div class="col-sm-1">' + response + '</div><div class="col-sm-3"><textarea name="row[' + response + '][origin]" data-label="' + response + ':origin" class="form-control datarow" rows="3" ></textarea></div><div class="col-sm-4"><textarea name="row[' + response + '][trans]" data-label="' + response + ':trans" class="form-control datarow" rows="3"></textarea></div><div class="col-sm-4"><textarea name="row[' + response + '][new_trans]" data-label="' + response + ':new_trans" class="form-control datarow" rows="3"></textarea></div></div></div>');
                $(window).scrollTop($("#addRow").offset().top);
            }
        });
    }

    function dellRow() {
        let token   = $('meta[name="csrf-token"]').attr('content');
        let lastId = $(".item").last().attr('id');

        $.ajax({
            url: "{{ route('index.store.item.delete', ['hash' => $project->hash])}}",
            type: 'POST',
            data: {
                _token: token,
                id: lastId,
            },
            success: function(response) {
                console.log(response);
                $(".item").last().remove();
                $(window).scrollTop($("#addRow").offset().top);
            }
        });
    }

</script>
@endpush
