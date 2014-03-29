<?php namespace Berpcor\RBAC;


/**
* RBAC - Laravel 4 RBAC
*
* @author Berpcor <berpcor@gmail.com>
* @license http://opensource.org/licenses/MIT
* @package berpcor/RBAC
*/
interface RBACInterface{
    /**
     * Create role in DB
     * @return bool
     */
    public static function createRole($name, $description);
    /**
     * Delete role from DB if role is not assigned to any user
     * @return bool
     */
    public static function deleteRole($id);
    /**
     * Create permission in DB
     * @return bool
     */
    public static function createPermission($name, $description, $action);
    /**
     * Delete permission from DB if no one user has this permission
     * @return bool
     */
    public static function deletePermission($id);
    /**
     * Assign some role to some user. If user already has some role except default then the role will be reassigned.
     * @return bool
     */
    public static function assignRoleToUser($user_id,$role_id);
    /**
     * Remove current assigned role. User get the 'default' role. If user already has default role then nothing will happen.
     * @return bool
     */
    public static function removeUsersRole($user_id);
    /**
     * The same action as removeUsersRole(){} does.
     * @return bool
     */
    public static function setDefaultRoleFor($user_id);
    /**
     * Attaches permissions to role. No need to deattach. Just attach with this method. 
     * It will overwrite existing permissions for role.
     * @return bool
     */
    public static function attachPermissionToRole($role_id, $permission_id);

    /**
     * Check if user has permission to get access to action
     * @return bool
     */
    public static function hasPermission($action);

}