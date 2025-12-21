<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Task extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'tasks';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'title',
        'category',
        'description',
        'voice_text',      // ← Nama kolom voice di database Anda
        'due_date',        // ← Nama kolom deadline di database Anda
        'status',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'due_date' => 'datetime',  // Changed to datetime for better handling
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /* =========================================================
     | RELATIONSHIPS
     ========================================================= */

    /**
     * Get the user that owns the task
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function attachments()
    {
        return $this->hasMany(TaskAttachment::class);
    }

    /* =========================================================
     | ACCESSORS (Alias untuk Compatibility)
     ========================================================= */

    /**
     * Alias: Get deadline (untuk compatibility dengan code yang pakai 'deadline')
     */
    public function getDeadlineAttribute()
    {
        return $this->due_date;
    }

    /**
     * Alias: Set deadline (untuk compatibility dengan code yang pakai 'deadline')
     */
    public function setDeadlineAttribute($value)
    {
        $this->attributes['due_date'] = $value;
    }

    /**
     * Alias: Get voice_record (untuk compatibility)
     */
    public function getVoiceRecordAttribute()
    {
        return $this->voice_text;
    }

    /**
     * Alias: Set voice_record (untuk compatibility)
     */
    public function setVoiceRecordAttribute($value)
    {
        $this->attributes['voice_text'] = $value;
    }

    /**
     * Get formatted deadline
     */
    public function getFormattedDeadlineAttribute(): ?string
    {
        if (!$this->due_date) {
            return null;
        }
        return Carbon::parse($this->due_date)->format('d M Y');
    }

    /**
     * Get deadline formatted with time
     */
    public function getDeadlineFormattedAttribute(): ?string
    {
        return $this->getFormattedDeadlineAttribute();
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute(): string
    {
        return $this->status === 'berjalan' ? 'In Progress' : 'Completed';
    }

    /* =========================================================
     | SCOPES
     ========================================================= */

    /**
     * Scope: Only pending/in-progress tasks
     */
    public function scopePending($query)
    {
        return $query->where('status', 'berjalan');
    }

    /**
     * Scope: Only completed tasks
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'selesai');
    }

    /**
     * Scope: Overdue tasks
     */
    public function scopeOverdue($query)
    {
        return $query->where('status', 'berjalan')
                     ->whereNotNull('due_date')
                     ->where('due_date', '<', Carbon::today());
    }

    /**
     * Scope: Due today
     */
    public function scopeDueToday($query)
    {
        return $query->where('status', 'berjalan')
                     ->whereNotNull('due_date')
                     ->whereDate('due_date', Carbon::today());
    }

    /**
     * Scope: Due soon (within X days)
     */
    public function scopeDueSoon($query, $days = 3)
    {
        $today = Carbon::today();
        return $query->where('status', 'berjalan')
                     ->whereNotNull('due_date')
                     ->whereDate('due_date', '>', $today)
                     ->whereDate('due_date', '<=', $today->copy()->addDays($days));
    }

    /* =========================================================
     | HELPER METHODS
     ========================================================= */

    /**
     * Check if task is overdue
     */
    public function isOverdue(): bool
    {
        if (!$this->due_date || $this->status === 'selesai') {
            return false;
        }
        return Carbon::parse($this->due_date)->isPast();
    }

    /**
     * Check if task is due today
     */
    public function isDueToday(): bool
    {
        if (!$this->due_date || $this->status === 'selesai') {
            return false;
        }
        return Carbon::parse($this->due_date)->isToday();
    }

    /**
     * Check if task is due soon
     */
    public function isDueSoon($days = 3): bool
    {
        if (!$this->due_date || $this->status === 'selesai') {
            return false;
        }

        $dueDate = Carbon::parse($this->due_date);
        $today = Carbon::today();

        return $dueDate->isFuture() && $dueDate->diffInDays($today) <= $days;
    }

    /**
     * Get days until deadline (negative if overdue)
     */
    public function getDaysUntilDeadline(): ?int
    {
        if (!$this->due_date) {
            return null;
        }
        return Carbon::now()->diffInDays(Carbon::parse($this->due_date), false);
    }

    /**
     * Get notification priority
     */
    public function getNotificationPriority(): string
    {
        if ($this->isOverdue()) {
            return 'high';
        }

        if ($this->isDueToday()) {
            return 'medium';
        }

        if ($this->isDueSoon()) {
            return 'low';
        }

        return 'none';
    }
}
