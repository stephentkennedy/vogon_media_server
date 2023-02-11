<?php 
global $user_model;
?>
<h2>User Profile Settings</h2>
<h4>User Roles</h4>
<form action="?ext={{ext_name}}&form=user_roles&force_reload=true&tab={{ext_name}}" method="post">
    <label for="roles">Role</label>
    <select name="roles" id="roles">
        <?php 
            foreach($roles as $role){
                $role_data = json_decode($role['data_content'], true);
                echo '<option value="'.$role['data_id'].'">'.$role_data['role_name'].'</option>';
            }
        ?>
        <option value="new">Add New Role</option>
    </select>
    <fieldset>
        <input type="hidden" name="role_id" id="role_id" value="">
        <legend>Role Settings</legend>
        <label for="role_name">Role Name</label>
        <input type="text" id="role_name" name="role_name" value="" placeholder="Enter a name...">
        <?php if($user_model->permission('admin_permissions')){ ?>
        <input type="checkbox" value="1" name="admin_permissions" id="admin_permissions" class="inline">
        <label for="admin_permissions" class="inline">Admin Permissions?</label><br>
        <p class="small">Additional Admin only permissions, such as being able to set user specific modifications to roles.</p>
        <?php } ?>
        <input type="checkbox" value="1" name="settings" class="inline" id="settings_check">
        <label class="inline" for="settings_check">Can Update Settings?</label><br>
        <p class="small">This role can update settings for the server.</p>
        <input type="checkbox" value="1" name="edit" class="inline" id="edit_check">
        <label class="inline" for="edit_check">Can Edit Records?</label><br>
        <p class="small">This role can edit records on the server.</p>
        <input type="checkbox" value="1" name="sys_info" class="inline" id="sys_info">
        <label class="inline" for="sys_info">Can See System Info?</label><br>
        <p class="small">This role can see system info and run high risk operations like tool scripts and imports.</p>
        <input type="checkbox" value="1" name="history" id="history" class="inline">
        <label class="inline" for="history">History Tracking?</label><br>
        <p class="small">Allow this user role to track history through media files?</p>
        <input type="checkbox" value="1" name="multi_session" id="multi_session" class="inline">
        <label class="inline" for="multi_session">Allow multiple sessions?</label><br>
        <p class="small">Allow users with this role to have multiple sessions under this profile? It is advised to turn off history tracking if this is enabled.</p>
        <input type="checkbox" value="1" name="req_password" class="inline" id="password_check">
        <label class="inline" for="password_check">Requires Password?</label><br>
        <p class="small">This role requires a password for login.</p>
        <button type="submit"><i class="fa fa-floppy-o"></i> Save</button>
    </fieldset>
</form>
<style>
    p.small{
        font-size: 0.75rem;
        margin-bottom: 1rem;
    }
</style>
<script type="text/javascript">
    var user_role_data = {
        endpoint: '<?= build_slug('ajax/ajax_user_role/user'); ?>',
        get: function(role_id){
            if(role_id == 'new'){
                $('#role_id').val(0);
                $('#role_name').val('');
                $('#admin_permissions').prop('checked', false);
                $('#settings_check').prop('checked', false);
                $('#edit_check').prop('checked', false);
                $('#sys_info').prop('checked', false);
                $('#password_check').prop('checked', true);
                $('#history').prop('checked', false);
                $('#multi_session').prop('checked', false);
                return;
            }
            $.get(user_role_data.endpoint, {
                role: role_id
            }, function(returned){
                //console.log(returned);
                $('#role_id').val(role_id);
                $('#role_name').val(returned.role_name);
                if(returned.admin_permissions == 1){
                    $('#admin_permissions').prop('checked', true);
                }else{
                    $('#admin_permissions').prop('checked', false);
                }
                if(returned.settings == 1){
                    $('#settings_check').prop('checked', true);
                }else{
                    $('#settings_check').prop('checked', false);
                }
                if(returned.edit == 1){
                    $('#edit_check').prop('checked', true);
                }else{
                    $('#edit_check').prop('checked', false);
                }
                if(returned.sys_info == 1){
                    $('#sys_info').prop('checked', true);
                }else{
                    $('#sys_info').prop('checked', false);
                }
                if(returned.password == 1){
                    $('#password_check').prop('checked', true);
                }else{
                    $('#password_check').prop('checked', false);
                }
                if(returned.history == 1){
                    $('#history').prop('checked', true);
                }else{
                    $('#history').prop('checked', false);
                }
                if(returned.multi_session == 1){
                    $('#multi_session').prop('checked', true);
                }else{
                    $('#multi_session').prop('checked', false);
                }
            });
        },
        init: function(){
            $('select[name="roles"]').on('change', function(){
                var select = $(this);
                var role_id = select.val();
                user_role_data.get(role_id);
            });
        }
    };
    $(document).ready(function(){
        //Get initial Value
        var select = $('select[name="roles"]');
        var role_id = select.val();
        user_role_data.get(role_id);

        //Bind select listener
        user_role_data.init();
    });
</script>