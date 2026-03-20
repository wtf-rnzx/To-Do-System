<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <div
        id="profile-image-uploader"
        class="mt-6 rounded-lg border border-gray-200 dark:border-gray-700 p-4"
        data-upload-url="{{ url('/api/profile/image') }}"
        data-remove-url="{{ url('/api/profile/image') }}"
        data-max-size="2097152"
        data-has-image="{{ $user->profile_image ? '1' : '0' }}"
    >
        <div class="flex items-center gap-4">
            <img
                data-avatar-image
                src="{{ $user->profile_image_url }}"
                data-avatar-fallback="{{ url('/images/default-avatar.svg') }}"
                alt="{{ $user->name }} avatar"
                class="h-20 w-20 rounded-full object-cover ring-2 ring-gray-200 dark:ring-gray-700"
            >

            <div class="flex-1">
                <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ __('Profile Photo') }}</p>
                <p class="mt-1 text-xs text-gray-600 dark:text-gray-400">
                    {{ __('JPEG, PNG, or WebP up to 2MB. Images are auto-cropped to square and optimized before upload.') }}
                </p>

                <div class="mt-3 flex flex-wrap items-center gap-2">
                    <input
                        type="file"
                        id="profile-image-input"
                        accept="image/jpeg,image/png,image/webp"
                        class="block w-full text-sm text-gray-700 dark:text-gray-300 file:mr-3 file:rounded-md file:border-0 file:bg-gray-100 dark:file:bg-gray-700 file:px-3 file:py-2 file:text-sm file:font-medium file:text-gray-700 dark:file:text-gray-200 hover:file:bg-gray-200 dark:hover:file:bg-gray-600"
                    >

                    <button
                        type="button"
                        id="profile-image-upload-btn"
                        class="inline-flex items-center px-4 py-2 bg-indigo-600 dark:bg-indigo-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 dark:hover:bg-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150 disabled:opacity-50"
                        disabled
                    >
                        {{ __('Upload Photo') }}
                    </button>

                    <button
                        type="button"
                        id="profile-image-remove-btn"
                        class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-200 uppercase tracking-widest hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150 disabled:opacity-50"
                        {{ $user->profile_image ? '' : 'disabled' }}
                    >
                        {{ __('Remove Photo') }}
                    </button>
                </div>

                <p id="profile-image-loading" class="mt-2 hidden text-xs text-indigo-600 dark:text-indigo-400"></p>
                <p id="profile-image-error" class="mt-2 hidden text-xs text-red-600 dark:text-red-400"></p>
            </div>
        </div>
    </div>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800 dark:text-gray-200">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification" class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600 dark:text-green-400">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600 dark:text-gray-400"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
