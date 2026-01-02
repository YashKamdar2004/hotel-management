<?php 

function alert($type,$msg){
    echo <<<alert
    <div class="alert alert-warning alert-dismissible fade show custom-alert" role="alert">
        <strong class="me-3">$msg</strong>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    alert;
}


?>