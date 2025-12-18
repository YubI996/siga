<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class WilayahScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $user = auth()->user();

        // Kalau tidak ada user (CLI, seeder, dsb) -> jangan batasi
        if (! $user) {
            return;
        }

        // Super admin atau yang punya permission khusus -> akses penuh
        if ($user->hasRole('super-admin') || $user->can('data.view_all')) {
            return;
        }

        $builder->where(function (Builder $q) use ($user) {
            // Penginput selalu boleh lihat data yang dia buat sendiri
            $q->where('created_by', $user->id);

            switch ($user->level_wilayah) {
                case 'rt':
                    if ($user->rt_id) {
                        $q->orWhere('rt_id', $user->rt_id);
                    }
                    break;

                case 'lurah':
                    if ($user->kelurahan_id) {
                        $q->orWhere('kelurahan_id', $user->kelurahan_id);
                    }
                    break;

                case 'camat':
                    if ($user->kecamatan_id) {
                        $q->orWhere('kecamatan_id', $user->kecamatan_id);
                    }
                    break;

                // kalau mau nanti tambahkan case lain (opd, dsb)
            }
        });
    }
}
