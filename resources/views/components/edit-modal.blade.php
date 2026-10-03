@props([
    'title' => '',
    'sizeClass' => 'modal-md',
    'id' => '',
])
    <div class="modal modal-danger fade" id="editModal-{{$id}}" tabindex="-1" role="dialog" aria-labelledby="Delete"
        aria-hidden="true">
        <div class="modal-dialog {{ $sizeClass }}" role="document">
            <div class="px-4 modal-content dark:bg-slate-900 dark:border-slate-800" style="border-radius: 5px;">
                <div class="modal-header border-b dark:border-slate-800">
                    <h5 class="modal-title dark:text-white">{{ $title }}</h5>
                </div>
                <div class="modal-body">
                    <div class="col-md-12">
                        <div class="row">
                            {{ $slot }}
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn" style="padding: 3px 20px; background: #f04438;color:white; border-radius:5px" data-dismiss="modal">Cancel</button>
                    <button id="Update" type="submit" style="padding: 3px 20px; background: #12b76a;color:white; border-radius:5px;margin-right: 5px" class="btn update">Update</button>
                </div>
            </div>
        </div>
    </div>
