<?php

declare(strict_types=1);

namespace WPJarvis\Foundation\ServiceProviders;

use WPJarvis\Core\Support\ServiceProvider;
use WPJarvis\Security\SecurityServiceProvider as CoreSecurityServiceProvider;
use WPJarvis\Security\Nonce;
use WPJarvis\Security\Capabilities;

class SecurityServiceProvider extends ServiceProvider
{
    /**
     * Core and modular providers to register.
     *
     * @var array<class-string<ServiceProvider>>
     */
    protected array $providers = [
        CoreSecurityServiceProvider::class,
        // Future: PolicyServiceProvider::class, etc.
    ];

    /**
     * Register the security services and aliases.
     *
     * @return void
     */
    public function register(): void
    {
        foreach ($this->providers as $provider) {
            $this->app->register($provider);
        }

        // Bind facades to core services
        $this->app->alias('nonce', Nonce::class);
        $this->app->alias('capabilities', Capabilities::class);
    }

    /**
     * Bootstrap any application-specific security behavior.
     *
     * @return void
     */
    public function boot(): void
    {
        // Fire a security boot event for custom extensions
        add_action('init', fn() => do_action('wpjarvis_security_init'));

        // Optional: Enforce strong password messaging
        if (config('security.enforce_strong_passwords', false)) {
            add_filter('password_hint', [$this, 'enforceStrongPasswordHint']);
        }

        // Optional: Block weak passwords in registration (future)
        // add_action('user_profile_update_errors', [$this, 'validatePasswordStrength'], 10, 3);
    }

    /**
     * Enhance password strength hint shown to users.
     *
     * @param string $hint
     * @return string
     */
    public function enforceStrongPasswordHint(string $hint): string
    {
        $message = __('Passwords should contain at least one uppercase letter, one lowercase letter, one number, and one special character.', 'wpjarvis');
        return trim($hint . ' ' . $message);
    }

    /**
     * Validate password strength (optional for future).
     *
     * @param \WP_Error $errors
     * @param bool $update
     * @param object $user
     */
    public function validatePasswordStrength(\WP_Error $errors, bool $update, object $user): void
    {
        $password = $_POST['pass1'] ?? '';

        if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).+$/', $password)) {
            $errors->add('weak_password', __('Password is too weak. Use upper/lowercase, number, and symbol.', 'wpjarvis'));
        }
    }
}