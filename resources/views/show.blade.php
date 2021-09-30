@extends('layout')

@section('content')
    <div class="container-fluid p-5">
        <div class="form-group row">
            <div class="col-sm-10">
                <h1>{{ __("Project name:")}} {{ $project->project_name ?? ''}} <br>{{ __("Lang:")}} {{ $project->localize->loc_name }}</h1>
            </div>
            <div class="col-sm-2">
               <a href="{{ route('index.download', $project->hash) }}" class="btn bg-primary" style="color:orange;"><i class="fa fa-cloud-download" aria-hidden="true"></i></a>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <form id="form" method="POST" action="{{ route('index.download', $project->hash) }}" enctype="multipart/form-data">
                @csrf
                @foreach ($project->localize->locItem as $item)
                    <div class="form-group row">
                        <div class="col-sm-1">
                            {{ $item->id }}
                        </div>
                        <div class="col-sm-3">
                            <textarea name="row[{{ $item->id }}][origin]" data-label="{{ $item->id }}:origin" class="form-control datarow" rows="3" readonly>{{ $item->origin ?? ''}}</textarea>
                        </div>
                        <div class="col-sm-4">
                            <textarea name="row[{{ $item->id }}][trans]" data-label="{{ $item->id }}:trans" class="form-control datarow" rows="3">{{ $item->trans ?? ''}}</textarea>
                        </div>
                        <div class="col-sm-4">
                            <textarea name="row[{{ $item->id }}][new_trans]" data-label="{{ $item->id }}:new_trans" class="form-control datarow" rows="3">{{ $item->new_trans ?? ''}}</textarea>
                        </div>
                    </div>
                @endforeach
                <div class="form-group row">
                    <div class="col-sm-8 offset-4">
                        <button type="submit" class="btn btn-primary"><?php echo __('Download!') ?> </button>
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
</script>
@endpush
