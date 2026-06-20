<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsletterSubscriber extends Model
{
    protected $fillable = ['name', 'email', 'whatsapp', 'is_active', 'source'];
    protected $table    = 'newsletter_subscribers';
}
