@props([
    'title' => '',
    'id' => '',
])
<div class="modal modal-danger fade" id="deleteModal-{{$id}}" tabindex="-1" role="dialog" aria-labelledby="Delete"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="px-4 modal-content dark:bg-slate-900 dark:border-slate-800">
            <div class="modal-header border-b dark:border-slate-800">
                <h5 class="modal-title dark:text-white">{{ $title }}</h5>
            </div>
            <div class="modal-body">
                <div class="col-md-12">
                    <div class="row">
                        <h4 class="text-center dark:text-slate-300">
                            Are you sure?
                            You want to <span class="text-danger font-bold">DELETE</span> This {{ $title }}?
                        </h4>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-t dark:border-slate-800">
                <button type="button" class="btn cancel_btn" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn save_btn delete">Delete</button>
            </div>
        </div>
    </div>
</div>
