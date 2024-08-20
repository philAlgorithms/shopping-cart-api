<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Handler\Paystack\PaystackCustomer;
use App\Models\Media\MediaFile;
use App\Models\Users\{Admin, Buyer, LogisticsPersonnel, Vendor};
use GuzzleHttp\Exception\ClientException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
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
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function admin(): HasOne
    {
        return $this->hasOne(Admin::class);
    }

    public function vendor(): HasOne
    {
        return $this->hasOne(Vendor::class);
    }

    public function buyer(): HasOne
    {
        return $this->hasOne(Buyer::class);
    }

    public function logisticsPersonnel(): HasOne
    {
        return $this->hasOne(LogisticsPersonnel::class);
    }

    /**
     * The user's avatar
     */
    public function avatar(): BelongsTo
    {
        return $this->belongsTo(MediaFile::class, 'avatar_id');
    }

    public function getNameAttribute(): string
    {
        return $this->first_name . " " . $this->last_name;
    }

    public function getAvatarUrlAttribute(): string
    {
        return $this->avatar->temporary_url ?? generateSvgUrlFromInitial($this->first_name);
    }

    public function getHasBvnAttribute(): bool
    {
        return !is_null($this->bvn);
    }

    public function getBvnIsVerifiedAttribute(): bool
    {
        return !$this->has_bvn && !is_null($this->bvn_verified_at) &&
            $this->bvn_verified_at > $this->bvn_declined_at;
    }

    public function getBvnIsVerifiableAttribute(): bool
    {
        return !$this->has_bvn && !$this->bvn->is_verified;
    }

    public function getBvnIsDeclinedAttribute(): bool
    {
        return !$this->has_bvn && !is_null($this->bvn_declined_at) &&
            $this->bvn_declined_at > $this->bvn_verified_at;
    }

    public function getBvnIsDeclinableAttribute(): bool
    {
        return !$this->has_bvn && !$this->bvn->is_declined;
    }

    /**
     * Verify BVN
     */
    public function verifyBvn(): bool
    {
        if ($this->is_verifiable && !$this->is_verified) {
            return $this->update([
                'bvn_verified_at' => now()
            ]);
        }
        return false;
    }

    /**
     * Decline BVN
     */
    public function declineBvn(): bool
    {
        if ($this->is_declinable && !$this->is_declined) {
            return $this->update([
                'bvn_declined_at' => now()
            ]);
        }
        return false;
    }

    public function getHasPassedKycVerificationAttribute(): bool{
        return $this->bvn_is_verified;
    }

    public function createPaystackCustomer()
    {
        {$request = (new PaystackCustomer)->create(
            email: $this->buyer->email ?? $this->vendor->email ?? $this->admin->email,
            first_name: $this->first_name,
            last_name: $this->last_name,
            phone_number: $this->phone_number ?? ''
        );

        if(is_array($request) && array_key_exists('data', $request))
        {
            if(is_array($request['data']) && array_key_exists('customer_code', $request['data']))
            {
                $this->update([
                    'paystack_customer_code' => $request['data']['customer_code']
                ]);

                return $this->refresh();
            }
        }}
        return false;
    }

    public function getIsPaystackCustomerAttribute(): bool{
        return ! is_null($this->paystack_customer_code);
    }

    public function paystackValidate(string $bvn, string $account_number, Bank|string $bank)
    {
        if(!$this->is_paystack_customer)
        {
            $this->createPaystackCustomer();
        }
        $this->refresh();

        try {
            (new PaystackCustomer)->validate(
                customer_code: $this->paystack_customer_code,
                bvn: $bvn,
                first_name: $this->first_name,
                last_name: $this->last_name,
                account_number: $account_number,
                bank_code: $bank instanceof Bank ? $bank->code : $bank
            );

            $this->update([
                'last_uploaded_bvn' => $bvn,
                'last_uploaded_bank_account_number' => $account_number,
                'last_uploaded_bank_id' => $bank instanceof Bank ? $bank->id : Bank::firstWhere('code', $bank)->id ?? null
            ]);
    
            return $this;
        } catch (ClientException $e) {
            throw ValidationException::withMessages([
                'bvn' => $e->getMessage()
            ]);
        }
    }
}
