@php
    $user = \Filament\Facades\Filament::auth()->user();
@endphp
<x-filament::page>
    <!-- Feature section with grid -->
    <div class="relative py-16 sm:py-24 lg:py-32">
        <div class="mx-auto max-w-md px-6 text-center sm:max-w-3xl lg:max-w-7xl lg:px-8">
            <p class="mt-2 text-3xl font-bold tracking-tight text-white-900 sm:text-4xl">Hi, {{ $user->name }}, welcome to WG-ADMIN portal!</p>
            <p class="mx-auto mt-5 max-w-prose text-xl text-gray-500">This portal allows you to manage and add your devices to the network, and access your <br> Virtual Private Network!</p>
            <div class="mt-12">
                <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3">
                    <div class="pt-6">
                        <div class="flow-root rounded-lg bg-gray-200 px-6 pb-8">
                            <div class="-mt-6">
                                <div>
                      <span class="inline-flex items-center justify-center rounded-md bg-gradient-to-r from-primary-500 to-primary-700 p-3 shadow-lg text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
</svg>

                      </span>
                                </div>
                                <h3 class="mt-8 text-lg font-medium tracking-tight text-gray-900">Your profile</h3>
                                <p class="mt-5 text-base text-gray-700">Access to your profile and change your settings and preferences.</p>
                                <p class="mt-5"><a href="{{ route('filament.pages.settings') }}" class="inline-flex items-center justify-center whitespace-nowrap rounded-md border border-transparent bg-gradient-to-r from-primary-500 to-primary-700 bg-origin-border px-4 py-2 text-base font-medium text-white shadow-sm hover:from-primary-600 hover:to-primary-800">Open your profile</a></p>
                            </div>
                        </div>
                    </div>

                    <div class="pt-6">
                        <div class="flow-root rounded-lg bg-gray-200 px-6 pb-8">
                            <div class="-mt-6">
                                <div>
                      <span class="inline-flex items-center justify-center rounded-md bg-gradient-to-r from-primary-500 to-primary-700 p-3 shadow-lg text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5h3m-6.75 2.25h10.5a2.25 2.25 0 002.25-2.25v-15a2.25 2.25 0 00-2.25-2.25H6.75A2.25 2.25 0 004.5 4.5v15a2.25 2.25 0 002.25 2.25z" />
</svg>

                      </span>
                                </div>
                                <h3 class="mt-8 text-lg font-medium tracking-tight text-gray-900">Your devices</h3>
                                <p class="mt-5 text-base text-gray-700">See all your devices that have access to your virtual private network.</p>
                                <p class="mt-5"><a href="{{ route('filament.resources.devices.index') }}" class="inline-flex items-center justify-center whitespace-nowrap rounded-md border border-transparent bg-gradient-to-r from-primary-500 to-primary-700 bg-origin-border px-4 py-2 text-base font-medium text-white shadow-sm hover:from-primary-600 hover:to-primary-800">Check you devices</a></p>
                            </div>
                        </div>
                    </div>

                    <div class="pt-6">
                        <div class="flow-root rounded-lg bg-gray-200 px-6 pb-8">
                            <div class="-mt-6">
                                <div>
                      <span class="inline-flex items-center justify-center rounded-md bg-gradient-to-r from-primary-500 to-primary-700 p-3 shadow-lg text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M16.712 4.33a9.027 9.027 0 011.652 1.306c.51.51.944 1.064 1.306 1.652M16.712 4.33l-3.448 4.138m3.448-4.138a9.014 9.014 0 00-9.424 0M19.67 7.288l-4.138 3.448m4.138-3.448a9.014 9.014 0 010 9.424m-4.138-5.976a3.736 3.736 0 00-.88-1.388 3.737 3.737 0 00-1.388-.88m2.268 2.268a3.765 3.765 0 010 2.528m-2.268-4.796a3.765 3.765 0 00-2.528 0m4.796 4.796c-.181.506-.475.982-.88 1.388a3.736 3.736 0 01-1.388.88m2.268-2.268l4.138 3.448m0 0a9.027 9.027 0 01-1.306 1.652c-.51.51-1.064.944-1.652 1.306m0 0l-3.448-4.138m3.448 4.138a9.014 9.014 0 01-9.424 0m5.976-4.138a3.765 3.765 0 01-2.528 0m0 0a3.736 3.736 0 01-1.388-.88 3.737 3.737 0 01-.88-1.388m2.268 2.268L7.288 19.67m0 0a9.024 9.024 0 01-1.652-1.306 9.027 9.027 0 01-1.306-1.652m0 0l4.138-3.448M4.33 16.712a9.014 9.014 0 010-9.424m4.138 5.976a3.765 3.765 0 010-2.528m0 0c.181-.506.475-.982.88-1.388a3.736 3.736 0 011.388-.88m-2.268 2.268L4.33 7.288m6.406 1.18L7.288 4.33m0 0a9.024 9.024 0 00-1.652 1.306A9.025 9.025 0 004.33 7.288" />
</svg>

                      </span>
                                </div>
                                <h3 class="mt-8 text-lg font-medium tracking-tight text-gray-900">User guide</h3>
                                <p class="mt-5 text-base text-gray-700">Read the user guide to learn how to connect your devices.</p>
                                <p class="mt-5"><a href="#" class="inline-flex items-center justify-center whitespace-nowrap rounded-md border border-transparent bg-gradient-to-r from-primary-500 to-primary-700 bg-origin-border px-4 py-2 text-base font-medium text-white shadow-sm hover:from-primary-600 hover:to-primary-800">Open user guide</a></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-filament::page>
