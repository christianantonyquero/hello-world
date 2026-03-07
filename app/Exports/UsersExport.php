<?php

namespace App\Exports;

use App\User;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class UsersExport implements FromQuery, WithHeadings, WithMapping
{
    public function headings(): array 
    {
        return [
            'email', 
            'name', 
            'phone',
            'date_of_birth', 
            'country',
            'opt_in_newsletter', 
            'umusic_email_opt_in',
            'umusic_sms_opt_in',
            'last_login_at', 
            'login_count',
            'created_at', 
        ];
    }
    public function map($user): array
    {
        return [
            $user->email,
            $user->name,
            $user->phone,
            isset($user->date_of_birth) ? $user->date_of_birth->format('Y-m-d') : null,
            $user->country,
            $user->opt_in_newsletter,
            $user->umusic_email_opt_in,
            $user->umusic_sms_opt_in,
            $user->last_login_at,
            $user->login_count,
            $user->created_at,
        ];
    }

    public function query()
    {
        // Return non admin and non test users accoutns
        return User::query()->where([
            ['is_admin', false], 
            ['is_tester', false]]);
    }
}
