import preset from '../../../../vendor/filament/filament/tailwind.config.preset'

export default {
    presets: [preset],
    content: [
        './app/Filament/App/**/*.php',
        './resources/views/filament/app/**/*.blade.php',
        './resources/views/filament/admin/tables/columns/cover.blade.php',
        './vendor/filament/**/*.blade.php',
        './vendor/jaocero/radio-deck/resources/views/**/*.blade.php',
        './vendor/diogogpinto/filament-auth-ui-enhancer/resources/**/*.blade.php',
    ],
}
