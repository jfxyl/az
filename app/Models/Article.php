<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $table = 'news_all';

    const CREATED_AT = 'creat_time';
    const UPDATED_AT = 'modify_time';

    protected $fillable = [
        'platform_id',
        'title',
        'media',
        'content',
        'origin_link',
        'label1',
        'label2',
        'label3',
        'label4',
        'label5',
        'label6',
        'label7',
        'label8',
        'label9',
        'label10',
        'label11',
        'label12',
        'label13',
        'label14',
        'label15',
        'pub_time',
    ];

}
