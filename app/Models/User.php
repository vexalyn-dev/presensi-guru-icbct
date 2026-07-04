<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string $role
 * @property string|null $phone
 * @property string|null $address
 * @property string|null $bio
 * @property string|null $photo
 * @property string|null $qr_code
 * @property string|null $qr_token
 * @property string|null $provider
 * @property string|null $provider_id
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * 
 * @property-read string $formatted_id
 * @property-read string $photo_url
 * @property-read string $qr_code_url
 * @property-read string $role_name
 * @property-read string $subject_names
 * @property-read int $total_hours
 * @property-read array $attendance_stats
 * 
 * @method static \Illuminate\Database\Eloquent\Builder|User teachers()
 * @method static \Illuminate\Database\Eloquent\Builder|User active()
 * @method static \Illuminate\Database\Eloquent\Builder|User inactive()
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'address',
        'bio',
        'photo',
        'qr_code',
        'qr_token',
        'provider',
        'provider_id',
        'is_active',
        'subject',
        'start_time',
        'end_time',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'photo_url',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    // ==========================================
    // BOOT / MODEL EVENTS
    // ==========================================
    
    /**
     * Boot the model and register model events.
     */
    protected static function boot(): void
    {
        parent::boot();
        
        // Auto-generate QR token when creating new teacher
        static::creating(function (User $user) {
            if ($user->role === 'guru' && empty($user->qr_token)) {
                $user->qr_token = Str::uuid()->toString();
            }
        });
    }

    // ==========================================
    // ROLE CHECK METHODS
    // ==========================================
    
    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is teacher
     */
    public function isTeacher(): bool
    {
        return $this->role === 'guru';
    }

    /**
     * Check if user has specific role
     */
    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    // ==========================================
    // SCOPES (Query Builder)
    // ==========================================
    
    /**
     * Scope: Filter only teachers (role = 'guru')
     * ⚠️ METHOD INI YANG MENGHILANGKAN ERROR teachers()
     */
    public function scopeTeachers($query)
    {
        return $query->where('role', 'guru');
    }

    /**
     * Scope: Filter only active users
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Filter only inactive users
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    // ==========================================
    // RELATIONSHIPS
    // ==========================================
    
    // /**
    //  * Get all subjects assigned to teacher
    //  */
    // public function subjects(): BelongsToMany
    // {
    //     return $this->belongsToMany(
    //         Subject::class, 
    //         'teacher_subjects',
    //         'user_id',
    //         'subject_id'
    //     )
    //     ->withPivot('class_id', 'semester', 'academic_year', 'hours_per_week', 'is_active')
    //     ->withTimestamps();
    // }

    // /**
    //  * Get only active subjects
    //  */
    // public function activeSubjects()
    // {
    //     return $this->subjects()->wherePivot('is_active', true);
    // }

    /**
     * Get all schedules
     */
    public function schedules()
    {
        return $this->hasMany(TeacherSchedule::class);
    }

    public function teachingSchedules()
    {
        return $this->hasMany(TeachingSchedule::class);
    }

    /**
     * Get all attendances
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class, 'user_id');
    }

    public function classAttendances()
    {
        return $this->hasMany(ClassAttendance::class);
    }

    // /**
    //  * Get classes where user is wali kelas
    //  */
    // public function classes(): HasMany
    // {
    //     return $this->hasMany(ClassRoom::class, 'teacher_id');
    // }

    // /**
    //  * Get classes through teacher_subjects pivot (for teaching schedule)
    //  */
    // public function teachingClasses(): HasManyThrough
    // {
    //     return $this->hasManyThrough(
    //         ClassRoom::class,
    //         TeacherSubject::class,
    //         'user_id',      // Foreign key on teacher_subjects table
    //         'id',           // Foreign key on classes table
    //         'id',           // Local key on users table
    //         'class_id'      // Local key on teacher_subjects table
    //     );
    // }

    /**
     * Get messages where this user is the "conversation owner"
     */
    public function messagesAsConversationUser(): HasMany
    {
        return $this->hasMany(Message::class, 'conversation_user_id');
    }

    // ==========================================
    // ACCESSORS / ATTRIBUTES
    // ==========================================
    
    /**
     * Get formatted teacher ID (e.g., GURU-0001)
     */
    public function getFormattedIdAttribute(): string
    {
        return 'GURU-' . str_pad($this->id, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get full URL for photo
     */
    public function getPhotoUrlAttribute(): string
    {
        return $this->photo 
            ? asset('storage/' . $this->photo) 
            : asset('images/default-teacher.png');
    }

    /**
     * Get full URL for QR code
     */
    public function getQrCodeUrlAttribute(): string
    {
        return $this->qr_code 
            ? asset('storage/' . $this->qr_code) 
            : '';
    }

    /**
     * Get role name with capitalization
     */
    public function getRoleNameAttribute(): string
    {
        return ucfirst($this->role);
    }

    /**
     * Get comma-separated list of active subject names
     */
    public function getSubjectNamesAttribute(): string
    {
        return $this->activeSubjects->pluck('name')->join(', ');
    }

    /**
     * Get total teaching hours per week from active subjects
     */
    public function getTotalHoursAttribute(): int
    {
        return $this->activeSubjects->sum('pivot.hours_per_week');
    }

    /**
     * Get attendance statistics
     */
    public function getAttendanceStatsAttribute(): array
    {
        return [
            'total' => $this->attendances()->count(),
            'hadir' => $this->attendances()->where('status', 'Hadir')->count(),
            'terlambat' => $this->attendances()->where('status', 'Terlambat')->count(),
            'izin' => $this->attendances()->where('status', 'Izin')->count(),
            'alpha' => $this->attendances()->where('status', 'Alpha')->count(),
        ];
    }

    // ==========================================
    // QR CODE METHODS
    // ==========================================
    
    public function generateQrCode(): string
    {
        if (empty($this->qr_token)) {
            $this->qr_token = Str::uuid()->toString();
            $this->save();
        }

        $qrData = json_encode([
            'teacher_id' => $this->id,
            'token' => $this->qr_token,
            'name' => $this->name,
            'email' => $this->email,
            'subject' => $this->subject,
        ], JSON_UNESCAPED_UNICODE);

        try {
            // Gunakan API external untuk generate format JPG murni tanpa error Imagick PHP extension
            $qrUrl = 'https://api.qrserver.com/v1/create-qr-code/';
            
            // Gunakan Laravel HTTP client tanpa verifikasi SSL (mengatasi masalah SSL di local Windows/XAMPP)
            $response = \Illuminate\Support\Facades\Http::withoutVerifying()->timeout(5)->get($qrUrl, [
                'size' => '400x400',
                'margin' => '2',
                'format' => 'jpeg',
                'data' => $qrData
            ]);

            if (!$response->successful()) {
                throw new \Exception('Gagal memuat QR dari API');
            }
            
            $qrCode = $response->body();
            $filename = 'qr_' . $this->id . '_' . time() . '.jpg';
        } catch (\Exception $e) {
            // Fallback ke SVG native jika internet/API mati
            $qrCode = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')
                ->size(400)
                ->errorCorrection('H')
                ->margin(2)
                ->generate($qrData);
                
            $filename = 'qr_' . $this->id . '_' . time() . '.svg';
        }

        $path = 'qrcodes/' . $filename;
        
        // Ensure directory exists
        Storage::disk('public')->makeDirectory('qrcodes');
        Storage::disk('public')->put($path, $qrCode);
        
        // Update user record with new QR path
        $this->update(['qr_code' => $path]);

        return $path;
    }

    /**
     * Regenerate QR Code (delete old, create new)
     */
    public function regenerateQrCode(): string
    {
        // Delete old QR code file if exists
        if ($this->qr_code && Storage::disk('public')->exists($this->qr_code)) {
            Storage::disk('public')->delete($this->qr_code);
        }

        // Generate new token
        $this->qr_token = Str::uuid()->toString();
        $this->save();

        // Generate and return new QR
        return $this->generateQrCode();
    }

    // ==========================================
    // UTILITY METHODS
    // ==========================================
    
    /**
     * Cek apakah guru ini dijadwalkan hari ini
     */
    public function isScheduledToday()
    {
        return TeacherSchedule::isScheduledToday($this->id);
    }

    /**
     * Ambil jadwal hari ini
     */
    public function getTodaySchedule()
    {
        return TeacherSchedule::getTodaySchedule($this->id);
    }

    /**
     * Ambil jadwal mengajar hari ini
     */
    public function getTodayTeachingSchedules()
    {
        return TeachingSchedule::getTodaySchedules($this->id);
    }

    /**
     * Hitung total kelas yang sudah diajar hari ini
     */
    public function getClassesTaughtToday()
    {
        return ClassAttendance::where('user_id', $this->id)
            ->where('date', today())
            ->whereNotNull('check_in_time')
            ->count();
    }

    /**
     * Check if teacher can be deleted (has no attendances)
     */
    public function canBeDeleted(): bool
    {
        return $this->attendances()->count() === 0;
    }

    /**
     * Get initial avatar for UI
     */
    public function getInitialsAttribute(): string
    {
        $nameParts = explode(' ', trim($this->name));
        $initials = '';
        
        // Take first letter of first and last name
        if (count($nameParts) >= 2) {
            $initials = strtoupper($nameParts[0][0] . end($nameParts)[0]);
        } elseif (count($nameParts) === 1) {
            $initials = strtoupper($nameParts[0][0] ?? '?');
        } else {
            $initials = '??';
        }
        
        return $initials;
    }
}