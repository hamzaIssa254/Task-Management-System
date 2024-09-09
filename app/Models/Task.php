<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
class Task extends Model
{
    use HasFactory, SoftDeletes;
    
    /**
     * Summary of guarded
     * @var array
     */
    protected $guarded = ['task_id', 'created_at', 'updated_at', 'deleted_at'];
    protected $primaryKey = 'task_id';
    public $incrementing = true;
    protected $table = 'user_task';

    const CREATED_AT = 'created_on';
    const UPDATED_AT = 'updated_on';
    public $timestamps = true;
    /**
     * Summary of getCreatedAtAttribute
     * @param mixed $value
     * @return string
     */
    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('d-m-Y H:i:s');
    }

    /**
     * Summary of getUpdatedAtAttribute
     * @param mixed $value
     * @return string
     */
    public function getUpdatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('d-m-Y H:i:s');
    }

    /**
     * Summary of setCreatedAtAttribute
     * @param mixed $value
     * @return void
     */
    public function setCreatedAtAttribute($value)
    {
        $this->attributes['created_at'] = Carbon::createFromFormat('d-m-Y H:i:s', $value)->format('Y-m-d H:i:s');
    }

    /**
     * Summary of setUpdatedAtAttribute
     * @param mixed $value
     * @return void
     */
    public function setUpdatedAtAttribute($value)
    {
        $this->attributes['updated_at'] = Carbon::createFromFormat('d-m-Y H:i:s', $value)->format('Y-m-d H:i:s');
    }

    /**
     * Summary of scopePriority
     * @param mixed $query
     * @param mixed $priority
     * @return mixed
     */
    public function scopePriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }
    /**
     * Summary of scopeStatus
     * @param mixed $query
     * @param mixed $status
     * @return mixed
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }
    /**
     * Summary of user
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
