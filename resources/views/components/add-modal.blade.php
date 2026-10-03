@props([
    'title' => '',
    'sizeClass' => '',
    'submitBtnClass' => '',
    'id' => 'addModal',
])
<div class="modal fade" id="{{ $id }}" tabindex="-1" role="dialog" aria-hidden="true" style="border-radius: 5px; z-index: 10050;">
    <div class="modal-dialog {{ $sizeClass }}" role="document">
        <div class="px-4 modal-content dark:bg-slate-900 dark:border-slate-800" style="border-radius: 5px">
            <div class="modal-header border-b dark:border-slate-800 d-flex justify-content-between align-items-center">
                <h5 class="modal-title dark:text-white mb-0">{{ $title }}</h5>
                <button type="button" class="close text-secondary" data-dismiss="modal" aria-label="Close" style="background: transparent; border: none; font-size: 24px; cursor: pointer;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="border-radius: 5px">
                {{ $slot }}
            </div>
            <div class="modal-footer">
                <button type="button" style="padding: 3px 20px; background: #f04438;color:white; border-radius:5px" class="btn" data-dismiss="modal">Cancel</button> 
                <button type="submit" style="padding: 3px 20px; background: #12b76a;color:white; border-radius:5px;margin-right: 5px" class="btn {{$submitBtnClass}} save">Save</button>
            </div>
        </div>
    </div>
</div>
