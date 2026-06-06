<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsletterSubscriber extends Model
{
    protected $fillable = ['name', 'email', 'is_active'];
    protected $table    = 'newsletter_subscribers';
}
