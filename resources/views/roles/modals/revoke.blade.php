<div class="modal fade" id="revoke-role-modal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="revoke_role" method="POST" action="">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h4>Revoke role</h4>
                </div>
                <div class="modal-body">
                    <p>You're about to revoke '<span id="name"></span>'.</p>
                    <p>This will render anything associated with this role unusable.</p>
                </div>
                <div class="modal-footer no-border">
                    <div class="text-right">
                        <button class="btn btn-default btn-sm" data-dismiss="modal">
                            <i class="fa fa-times"></i>&nbsp;Cancel
                        </button>
                        <button type="submit" class="btn btn-warning btn-sm">
                            <i class="fa fa-ban"></i>&nbsp;Revoke
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
