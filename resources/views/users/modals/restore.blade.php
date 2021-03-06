<div class="modal fade" id="restore-user-modal">
    <div class="modal-dialog" user="document">
        <div class="modal-content">
            <form id="restore_user" method="POST" action="">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h4>Restore user</h4>
                </div>
                <div class="modal-body">
                    <p>You're about to restore '<span id="name"></span>'.</p>
                    <p>This will bring the user back to normal usage.</p>
                </div>
                <div class="modal-footer no-border">
                    <div class="text-right">
                        <button class="btn btn-default btn-sm" data-dismiss="modal">
                            <i class="fa fa-times"></i>&nbsp;Cancel
                        </button>
                        <button type="submit" class="btn btn-success btn-sm">
                            <i class="fa fa-refresh"></i>&nbsp;Restore
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
