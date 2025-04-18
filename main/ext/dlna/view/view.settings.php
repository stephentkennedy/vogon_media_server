<h2>DLNA Server Settings</h2>
<form action="?ext={{ext_name}}&form=main_dlna&force_reload=true" method="post">
    <label>Server Display Name<br>
        <input type="text" name="friendly_name" value="<?php 
            if(empty($_SESSION['dlna_friendly_name'])){
                $_SESSION['dlna_friendly_name'] = 'Vogon UPnP Server';
            }
            echo $_SESSION['dlna_friendly_name'];
        ?>">
    </label>
    <label>Bound IP<br>
        <small>This is the IP address that devices will access to retrieve content</small><br>
        <input type="text" name="bound_ip" value="<?php 
            if(empty($_SESSION['dlna_bound_ip'])){
                $_SESSION['dlna_bound_ip'] = '127.0.0.1';
            }
            echo $_SESSION['dlna_bound_ip'];
        ?>">
    </label>
    <label>
        <input type="checkbox" name="xsendfile" value="1" <?php if(!empty($_SESSION['dlna_xsendfile'])) echo 'checked'; ?>>
        Use Apache X-Sendfile for streaming
        <small>Requires mod_xsendfile installed and configured in the VirtualHost.</small>
    </label>
    <button type="submit"><i class="fa fa-floppy-o"></i> Save</button>
</form>