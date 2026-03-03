<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    protected $fillable = [
        'advert_id',
        'buyer_id',
        'seller_id',
        'last_message_at',
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
    ];

    public function advert()
    {
        return $this->belongsTo(Advert::class);
    }

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function messages()
    {
        return $this->hasMany(ConversationMessage::class)->orderBy('id');
    }

    public function latestMessage()
    {
        return $this->hasOne(ConversationMessage::class)->latestOfMany('id');
    }
}

