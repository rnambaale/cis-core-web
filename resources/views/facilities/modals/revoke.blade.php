<div class="modal fade" id="revoke-facility-modal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="revoke_facility" method="POST" action="">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h4>Revoke facility</h4>
                </div>
                <div class="modal-body">
                    <p>You're about to revoke '<span id="name"></span>'.</p>
                    <p>This will render anything associated with this facility unusable.</p>
                </div>
                <div class="modal-footer">
                    <div class="text-right">
                        <button class="btn btn-secondary btn-sm" data-dismiss="modal">
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
