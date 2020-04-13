<div class="modal fade" id="destroy-facility-modal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="destroy_facility" method="POST" action="">
                @csrf
                @method('DELETE')
                <div class="modal-header">
                    <h4>Delete facility</h4>
                </div>
                <div class="modal-body">
                    <p>You're about to permanently delete '<span id="name"></span>' from storage.</p>
                    <p>This action is irreversible.</p>
                </div>
                <div class="modal-footer">
                    <div class="text-right">
                        <button class="btn btn-secondary btn-sm" data-dismiss="modal">
                            <i class="fa fa-times"></i>&nbsp;Cancel
                        </button>
                        <button type="submit" class="btn btn-danger btn-sm">
                            <i class="fa fa-trash"></i>&nbsp;Delete
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
