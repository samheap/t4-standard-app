<?php

namespace App\Modules\Contact\Models;

use  T4\Orm\Model;

class Contact
       extends Model
{
    static protected $schema = [
        'table' => 'contact',
        'columns' => [
            'datetime' => ['type'=>'datetime'],
            'email' => ['type' => 'string'],
            'name' => ['type' => 'string'],
            'message' =>['type' => 'text'],
            '__user_id' => ['type' => 'string'],
        ],
    ];

}