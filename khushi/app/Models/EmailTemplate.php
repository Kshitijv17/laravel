<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmailTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'subject',
        'body',
        'from_name',
        'from_email',
        'variables',
        'type',
        'category',
        'is_default',
        'status'
    ];

    protected $casts = [
        'variables' => 'array',
        'is_default' => 'boolean',
        'status' => 'boolean',
    ];

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    // Accessors
    public function getIsActiveAttribute()
    {
        return $this->status;
    }

    // Methods
    public function render($data = [])
    {
        $subject = $this->subject;
        $body = $this->body;
        
        foreach ($data as $key => $value) {
            $subject = str_replace('{{' . $key . '}}', $value, $subject);
            $body = str_replace('{{' . $key . '}}', $value, $body);
        }
        
        return [
            'subject' => $subject,
            'body' => $body,
            'from_name' => $this->from_name,
            'from_email' => $this->from_email
        ];
    }
}
