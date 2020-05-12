<div class="modal fade" id="adminModal" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Session Variables</h4><br>
            </div>
            <div class="modal-body">
                <p><?php
                    if (session_status() == PHP_SESSION_NONE) {
                        session_start();
                    }
                    foreach ($_SESSION as $key=>$val)
                        echo $key.": ".$val."<br/>";
                    ?></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>