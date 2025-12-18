<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        {{-- Name --}}
        <div>
            <x-input-label for="name" :value="__('Nama')" />
            <x-text-input
                id="name"
                class="block mt-1 w-full"
                type="text"
                name="name"
                :value="old('name')"
                required
                autofocus
                autocomplete="name"
            />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        {{-- Email Address --}}
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input
                id="email"
                class="block mt-1 w-full"
                type="email"
                name="email"
                :value="old('email')"
                required
                autocomplete="username"
            />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        {{-- Password --}}
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input
                id="password"
                class="block mt-1 w-full"
                type="password"
                name="password"
                required
                autocomplete="new-password"
            />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        {{-- Confirm Password --}}
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Konfirmasi Password')" />

            <x-text-input
                id="password_confirmation"
                class="block mt-1 w-full"
                type="password"
                name="password_confirmation"
                required
                autocomplete="new-password"
            />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        {{-- Level Wilayah --}}
        <div class="mt-6">
            <x-input-label for="level_wilayah" :value="__('Level Wilayah')" />

            <select
                id="level_wilayah"
                name="level_wilayah"
                class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                required
            >
                <option value="">{{ __('-- Pilih level wilayah --') }}</option>
                <option value="operator" @selected(old('level_wilayah') === 'operator')>Operator</option>
                <option value="rt" @selected(old('level_wilayah') === 'rt')>RT</option>
                <option value="lurah" @selected(old('level_wilayah') === 'lurah')>Lurah</option>
                <option value="camat" @selected(old('level_wilayah') === 'camat')>Camat</option>
                {{-- super-admin biasanya dibuat manual oleh admin, jadi tidak ditampilkan di sini --}}
            </select>

            <x-input-error :messages="$errors->get('level_wilayah')" class="mt-2" />
        </div>

        {{-- Kecamatan --}}
        <div class="mt-4">
            <x-input-label for="kecamatan_id" :value="__('Kecamatan')" />

            <select
                id="kecamatan_id"
                name="kecamatan_id"
                class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            >
                <option value="">{{ __('-- Pilih kecamatan --') }}</option>
                @isset($kecamatans)
                    @foreach ($kecamatans as $kecamatan)
                        <option
                            value="{{ $kecamatan->id }}"
                            @selected(old('kecamatan_id') == $kecamatan->id)
                        >
                            {{ $kecamatan->nama }}
                        </option>
                    @endforeach
                @endisset
            </select>

            <x-input-error :messages="$errors->get('kecamatan_id')" class="mt-2" />
        </div>

        {{-- Kelurahan --}}
        <div class="mt-4">
            <x-input-label for="kelurahan_id" :value="__('Kelurahan')" />

            <select
                id="kelurahan_id"
                name="kelurahan_id"
                class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            >
                <option value="">{{ __('-- Pilih kelurahan --') }}</option>
                @isset($kelurahans)
                    @foreach ($kelurahans as $kelurahan)
                        <option
                            value="{{ $kelurahan->id }}"
                            @selected(old('kelurahan_id') == $kelurahan->id)
                        >
                            {{ $kelurahan->nama }}
                        </option>
                    @endforeach
                @endisset
            </select>

            <x-input-error :messages="$errors->get('kelurahan_id')" class="mt-2" />
        </div>

        {{-- RT --}}
        <div class="mt-4">
            <x-input-label for="rt_id" :value="__('RT')" />

            <select
                id="rt_id"
                name="rt_id"
                class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            >
                <option value="">{{ __('-- Pilih RT --') }}</option>
                @isset($rts)
                    @foreach ($rts as $rt)
                        <option
                            value="{{ $rt->id }}"
                            @selected(old('rt_id') == $rt->id)
                        >
                            RT {{ $rt->kode_rt }} @if($rt->kode_rw) / RW {{ $rt->kode_rw }} @endif
                        </option>
                    @endforeach
                @endisset
            </select>

            <x-input-error :messages="$errors->get('rt_id')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-6">
            <a
                class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                href="{{ route('login') }}"
            >
                {{ __('Sudah terdaftar?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Daftar') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
