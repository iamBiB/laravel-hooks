<a href="https://github.com/iambib/laravel-hooks/releases/latest"><img src="https://img.shields.io/github/v/release/iambib/laravel-hooks.svg?style=flat-square" alt="" data-canonical-src="https://img.shields.io/github/v/release/iambib/laravel-hooks.svg?style=flat-square" style="max-width: 100%;"></a>
# Laravel Hooks & Plugins
A package that allows you to have hooks and plugins for your laravel application
## Description
Wordpress style hooks and plugins for laravel

## Installation
```shell
    composer require iambib/laravel-hooks
```
`config/app.php`
```php
	'providers'       => [
	    ...
	    iAmBiB\Hooks\Providers\HookServiceProvider::class,
	]
	'aliases'       => [
	    ...
	    'Hooks' => iAmBiB\Hooks\Facades\Hooks::class,
	]
```
```shell
    php artisan vendor:publish --tag=iambib-hooks
```
## Usage
See the demo plugin in app/Plugins
to execute a hook
```php
    execute_hook('hook_name')
```
# Support
Hey dude! If you like it .. well <g-emoji class="g-emoji" alias="beers" fallback-src="https://github.githubassets.com/images/icons/emoji/unicode/1f37b.png"><img class="emoji" alt="beers" height="20" width="20" src="https://github.githubassets.com/images/icons/emoji/unicode/1f37b.png"></g-emoji> or a <g-emoji class="g-emoji" alias="coffee" fallback-src="https://github.githubassets.com/images/icons/emoji/unicode/2615.png"><img class="emoji" alt="coffee" height="20" width="20" src="https://github.githubassets.com/images/icons/emoji/unicode/2615.png"></g-emoji> would be nice :D<br />

<a href="https://www.buymeacoffee.com/fhc0C7A" target="_blank" rel="nofollow"><img src="https://www.buymeacoffee.com/assets/img/custom_images/black_img.png" alt="coffee" data-canonical-src="https://www.buymeacoffee.com/assets/img/custom_images/black_img.png" style="max-width: 100%;"></a>