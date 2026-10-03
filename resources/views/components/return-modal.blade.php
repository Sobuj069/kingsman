@props([
    'title' => '',
    'id' => '',
])
<div class="modal modal-danger fade" id="returnModal-{{$id}}" tabindex="-1" role="dialog" aria-labelledby="Delete"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="px-4 modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ $title }}</h5>
            </div>
            <div class="modal-body">
                <div class="col-md-12">
                    <div class="row">
                        <h4 class="text-center">
                            Are you sure?
                            You want to <span class="text-danger">Return</span> This {{ $title }}?
                        </h4>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-danger ">Return</button>
            </div>
        </div>
    </div>
</div>
