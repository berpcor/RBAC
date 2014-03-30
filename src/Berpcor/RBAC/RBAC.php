<?php namespace Berpcor\RBAC;


use User;
use Role;
use Permission;
use Auth;
use Cache;
use Route;

/**
* RBAC - Laravel 4 RBAC
*
* @author Berpcor <berpcor@gmail.com>
* @license http://opensource.org/licenses/MIT
* @package berpcor/RBAC
*/
class RBAC implements RBACInterface
{
    public static function test(){
        
        return 123;
    }

    public static function createRole($name, $description){
        if(strlen($name)>128){
            throw new \Exception('Имя роли слишком длинное');
        }

        $count = count(Role::where('name','=',$name)->get());
        if($count>0){
            throw new \Exception('Роль с таким именем уже существует.');
        }

        $role = new Role();
        $role->name = $name;
        $role->description = $description;
        $role->save();
        return true;
    }

    public static function deleteRole($id){
        if($id==1){
            throw new \Exception('Нельзя удалить базовую роль.');
        }
        $count = User::where('role_id','=',$id)->count();
        if($count>0){
            throw new \Exception('Данная роль назначена одному или нескольким пользователям. Для того, чтобы удалить данную роль, нужно отвязать ее у всех пользователей.');
        }
        $role = Role::destroy($id);
        return true;
    }

    public static function createPermission($name, $description, $action){
        if(strlen($name)>128 || strlen($action)>128){
            throw new \Exception('Имя и экшн не может быть длиннее 128 символов.');
        }
        $count = count(Permission::where('name','=',$name)->get());
        if($count>0){
            throw new \Exception('Разрешение с таким именем уже существует.');
        }
        $count = count(Permission::where('action','=',$action)->get());
        if($count>0){
            throw new \Exception('Разрешение с таким экшеном уже существует.');
        }
        $permission = new Permission();
        $permission->name = $name;
        $permission->description = $description;
        $permission->action = $action;
        $permission->save();
        $permissions = Permission::all();
        // Put permissions in cache for 24 hours
        if (Cache::has('permissions'))
        {
            Cache::forget('permissions');
        }
        $p = Permission::lists('action');
        Cache::put('permissions', $p, 1440);

        return true;
    }

    public static function deletePermission($id){
        $count = count(Permission::find($id)->roles()->get());
        if($count>0){
            throw new \Exception('Нельзя удалить разрешение до тех пор пока оно принадлежит хоть одной роли. Чтобы удалить разрешение, нужно отвязать его ото всех ролей.');
        }
        $permission = Permission::destroy($id);
        // Put permissions in cache for 24 hours
        if (Cache::has('permissions'))
        {
            Cache::forget('permissions');
        }
        $p = Permission::lists('action');
        Cache::put('permissions', $p, 1440);
        return true;
    }

    public static function assignRoleToUser($user_id,$role_id){
        $user = User::find($user_id);
        if(count($user)==0){
            throw new \Exception('Пользователь с указанным ID не найден.');
        }
        $role = Role::find($role_id);
        if(count($role)==0){
            throw new \Exception('Роли с указанным ID не найдено.');
        }
        $user->role_id = $role_id;
        $user->save();
        return true;
    }

    public static function removeUsersRole($user_id){
        $user = User::find($user_id);
        if(count($user)==0){
            throw new \Exception('Пользователь с указанным ID не найден.');
        }
        $user->role_id = 'default';
        $user->save();
    }

    public static function setDefaultRoleFor($user_id){
        $user = User::find($user_id);
        if(count($user)==0){
            throw new \Exception('Пользователь с указанным ID не найден.');
        }
        $user->role_id = 'default';
        $user->save();
    } 
    public static function attachPermissionToRole($role_id, $permission_id){
        if($role_id==1){
            throw new \Exception('Базовой роли нельзя назначать пермишены.');
        }
        $count = Role::where('id','=',$role_id)->count();
        if($count==0){
            throw new \Exception('Указанной роли не найдено.');
        }
        if(!is_array($permission_id)){
            throw new \Exception('Разрешения должны передаваться в массиве.');
        }
        Role::find($role_id)->permissions()->detach();
        foreach($permission_id as $k){
            $role = Role::find($role_id);

            $role->permissions()->attach($k);
        }
        

    } 
    public static function hasPermission($action){

        if (!Auth::check()){
            return false;
        }
        $user = User::with(array('role.permissions' => function($query) use ($action) {
            $query->where('action','=',$action);
        }))->where('id','=', Auth::user()->id)->first();

        if ($user->role->permissions->isEmpty()){
            //return 'Нет разрешения';
            return false;
        }
        else {
            return true;
        }
    } 

    public static function filterMethod(){

        if (!Auth::check()){
            return false;
        }
        $action = Route::currentRouteAction();

        if (Cache::has('permissions'))
        {
            $p = Cache::get('permissions');
        }
        else {
            $p = Permission::lists('action');
        }

        // Checking if current action is protected
        if(in_array($action, $p)){
            //return 'запрещенное действие';
            // Check if current user has permission to get this action
            // 
            // NEED TO CACHE
            $actionToCheck = $action;
            $user = User::with(array('role.permissions' => function($query) use ($actionToCheck) {
                $query->where('action','=',$actionToCheck);
            }))->where('id','=', Auth::user()->id)->first();

            if ($user->role->permissions->isEmpty()){
                //return 'Нет разрешения';
                return false;
                
            }
            else {
                //return 'Есть разрешение';
                return true;
            }
        }
        return true;

    } 


}


