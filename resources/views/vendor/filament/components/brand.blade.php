<div
    x-data="{ mode: this.theme = localStorage.getItem('theme') || (this.isSystemDark() ? 'dark' : 'light') }"
    x-on:dark-mode-toggled.window="mode = $event.detail"
>
    <span x-show="mode === 'light'" style="display: none;">
        <img width="150" src="{{ mix('img/svg/wg-admin-light.svg') }}" alt="WG-ADMIN">
    </span>

    <span x-show="mode === 'dark'" style="display: none;">
        <img width="150" src="{{ mix('img/svg/wg-admin-dark.svg') }}" alt="WG-ADMIN">
    </span>
</div>
