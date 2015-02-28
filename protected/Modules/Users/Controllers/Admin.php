<?php

namespace App\Modules\Users\Controllers;

use App\Components\Admin\Controller;
use App\Models\User;
use App\Models\Role;

class Admin
    extends Controller
{
    public function actionDefault()
    {
        $this->data->items = User::findAll();
    }

    public function actionRoleUser($id, $namerole = null)
    {
        if (null == $namerole) {
            $users = User::findByPK($id);
            $this->data->addrole = array_diff(Role::findAll()->collect('title'), $users->roles->collect('title'));
            $this->data->users = $users->email;
            $this->data->userrole = $users->roles;
            $this->data->id = $id;
        } else {
            $user = User::findByPk($id);
            $role = Role::findByTitle($namerole);
            $user->roles->append($role);
            $user->save();
            $this->redirect('/admin/users/RoleUser?id=' . $id);
        }
    }

    public function actionRoles()
    {
        $this->data->items = Role::findAll();
    }

    public function actionEditRole($name = 'new')
    {
        if ('new' == $name) {
            $this->data->item = new Role();

        } else {
            $this->data->item = Role::findByName($name);
        }
    }

    public function actionSave()
    {
        $id = $this->app->request->post->id;

        if (empty($id)) {
            $item = new Role();

        } else {
            $item = Role::findByPK($id);
        }

        $item->fill($this->app->request->post);
        $item->save();
        $this->redirect('/admin/users/roles/');
    }

    public function actionDeleteRoleUser($id, $namerole, $idrole)
    {
        if ($namerole != 'admin') {
            $user = User::findByPk($id);
            unset($user->roles[$idrole]);
            $user->save();
            $this->redirect('/admin/users/RoleUser?id=' . $id);
        }
    }

    public function actionCheckPassword($id, $password = null)
    {
        if (null == $password) {
            $this->data->user = User::findByPk($id);
        } else {
            $user = User::findByPk($id);
            $user->password = \T4\Crypt\Helpers::hashPassword($password);
            $user->save();
            $this->redirect('admin/users');
        }
    }

    public function actionDeleteRole($name)
    {
        Role::findByName($name)->delete();
        $this->redirect('/admin/users/Roles');
    }
}