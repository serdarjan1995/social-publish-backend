<?php
use App\Model\Permission;
use App\Model\Role;
use Illuminate\Database\Seeder;

class PermissionRoleTableSeeder extends Seeder
{
    public function run()
    {
        $s_admin_permissions = Permission::all();
        Role::findOrFail(1)->permissions()->sync($s_admin_permissions->pluck('id'));

        $admin_permissions = $s_admin_permissions->filter(function ($permission) {
            return substr($permission->name, 0, 17) != 'social_media_api_'
                && substr($permission->name, 0, 5) != 'role_'
                && substr($permission->name, 0, 6) != 'proxy_'
                && substr($permission->name, 0, 11) != 'permission_';
        });
        Role::findOrFail(2)->permissions()->sync($admin_permissions);


        $agency_permissions = $s_admin_permissions->filter(function ($permission) {
            return (substr($permission->name, 0, 13) != 'social_media_'
                    && substr($permission->name, 0, 5) != 'role_'
                    && substr($permission->name, 0, 5) != 'user_'
                    && substr($permission->name, 0, 8) != 'payment_'
                    && substr($permission->name, 0, 6) != 'proxy_'
                    && substr($permission->name, 0, 11) != 'permission_')
                || substr($permission->name, 0, 12) == 'user_profile'
                || $permission->name== 'social_media_access'
                || $permission->name== 'social_media_list'
                || $permission->name== 'payment_user_access';
        });
        Role::findOrFail(4)->permissions()->sync($agency_permissions);

        $user_permissions = $s_admin_permissions->filter(function ($permission) {
            return (substr($permission->name, 0, 13) != 'social_media_'
                    && substr($permission->name, 0, 5) != 'role_'
                    && substr($permission->name, 0, 5) != 'user_'
                    && substr($permission->name, 0, 8) != 'payment_'
                    && substr($permission->name, 0, 6) != 'proxy_'
                    && substr($permission->name, 0, 11) != 'permission_'
                    && substr($permission->name, 0, 7) != 'agency_')
                || substr($permission->name, 0, 12) == 'user_profile'
                || $permission->name== 'social_media_access'
                || $permission->name== 'social_media_list'
                || $permission->name== 'payment_user_access';
        });
        Role::findOrFail(5)->permissions()->sync($user_permissions);


        $agency_user_permissions = $s_admin_permissions->filter(function ($permission) {
            return (substr($permission->name, 0, 13) != 'social_media_'
                    && substr($permission->name, 0, 5) != 'role_'
                    && substr($permission->name, 0, 5) != 'user_'
                    && substr($permission->name, 0, 8) != 'payment_'
                    && substr($permission->name, 0, 6) != 'proxy_'
                    && substr($permission->name, 0, 11) != 'permission_'
                    && substr($permission->name, 0, 7) != 'agency_'
                    && substr($permission->name, 0, 8) != 'account_')
                || substr($permission->name, 0, 12) == 'user_profile'
                || $permission->name== 'social_media_access'
                || $permission->name== 'social_media_list'
                || $permission->name== 'payment_user_access'
                || $permission->name== 'account_show';
        });
        Role::findOrFail(6)->permissions()->sync($agency_user_permissions);
    }
}
