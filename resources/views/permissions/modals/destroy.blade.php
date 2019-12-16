<div class="modal fade" id="destroy-permission-modal">
    <div class="modal-dialog" user="document">
        <div class="modal-content">
            <form id="destroy_permission" method="POST" action="">
                @csrf
                @method('DELETE')
                <div class="modal-header">
                    <h4>Delete permission</h4>
                </div>
                <div class="modal-body">
                    <p>You're about to permanently delete '<span id="name"></span>' from storage.</p>
                    <p>This action is irreversible.</p>
                </div>
                <div class="modal-footer no-border">
                    <div class="text-right">
                        <button class="btn btn-default btn-sm" data-dismiss="modal">
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
