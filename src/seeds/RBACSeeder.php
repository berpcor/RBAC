<?php

use Illuminate\Database\Seeder;
use Role;

class RBACSeeder extends Seeder {

    public function run()
    {
        Eloquent::unguard();
        Role::create(array('name' => 'default','description'=>'Стандартная роль, назначаемая пользователю по умолчанию. Запрещает совершение всех запрещенных действий.'));
    }

}
