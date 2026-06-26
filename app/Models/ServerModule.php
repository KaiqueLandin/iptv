<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServerModule extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'module_key',
        'description',
        'is_active',
        'server_hostname',
        'server_port',
        'use_ssl',
        'credentials',
        'settings',
        'provision_script',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'server_port' => 'integer',
        'use_ssl' => 'boolean',
        'credentials' => 'encrypted:array',
        'settings' => 'array',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getApiUrl(): string
    {
        $protocol = $this->use_ssl ? 'https' : 'http';
        $port = $this->server_port ? ":{$this->server_port}" : '';
        
        return "{$protocol}://{$this->server_hostname}{$port}";
    }
}
