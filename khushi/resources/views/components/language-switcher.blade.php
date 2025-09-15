<div class="language-switcher d-inline-block me-2">
    <a href="{{ route('locale.switch', 'en') }}" class="btn btn-outline-primary btn-sm {{ session('locale', 'en') === 'en' ? 'active' : '' }}">
        🇺🇸 EN
    </a>
    <a href="{{ route('locale.switch', 'es') }}" class="btn btn-outline-primary btn-sm {{ session('locale', 'en') === 'es' ? 'active' : '' }}">
        🇪🇸 ES
    </a>
</div>

<style>
.language-switcher .flag-icon {
    margin-right: 8px;
}
.language-switcher .dropdown-item.active {
    background-color: var(--bs-primary);
    color: white;
}
.language-switcher .dropdown-item:hover {
    background-color: var(--bs-light);
}
</style>
