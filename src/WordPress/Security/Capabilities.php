<?php

declare(strict_types=1);

namespace WPJarvis\Security;

/**
 * Provides a static API for managing and checking WordPress user capabilities.
 */
class Capabilities
{
    /**
     * Check if the current user has a specific capability.
     *
     * @param string $capability The capability to check for.
     * @param int|null $objectId Optional object ID for meta capability checks.
     * @return bool True if the user has the capability.
     */
    public static function can(string $capability, ?int $objectId = null): bool
    {
        return current_user_can($capability, $objectId);
    }

    /**
     * Check if a specific user has a capability.
     *
     * @param int $userId The user ID.
     * @param string $capability The capability to check for.
     * @param int|null $objectId Optional object ID for meta capability checks.
     * @return bool True if the user has the capability.
     */
    public static function userCan(int $userId, string $capability, ?int $objectId = null): bool
    {
        return user_can($userId, $capability, $objectId);
    }

    /**
     * Check if the current user has any of the specified capabilities.
     *
     * @param array<int, string> $capabilities List of capabilities.
     * @return bool True if the user has at least one capability.
     */
    public static function canAny(array $capabilities): bool
    {
        foreach ($capabilities as $capability) {
            if (self::can($capability)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if the current user has all of the specified capabilities.
     *
     * @param array<int, string> $capabilities List of capabilities.
     * @return bool True if the user has all capabilities.
     */
    public static function canAll(array $capabilities): bool
    {
        foreach ($capabilities as $capability) {
            if (!self::can($capability)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Authorize a capability, throwing an exception if unauthorized.
     *
     * @param string $capability Capability to check.
     * @param string $message Optional error message.
     * @return void
     * @throws \Exception If not authorized.
     */
    public static function authorize(string $capability, string $message = 'You do not have permission to perform this action.'): void
    {
        if (!self::can($capability)) {
            throw new \Exception($message);
        }
    }

    /**
     * Add one or more capabilities to a role.
     *
     * @param string $role Role name.
     * @param string|array<int, string> $capabilities Capability or list of capabilities.
     * @return bool True if the role exists and capabilities were added.
     */
    public static function addToRole(string $role, string|array $capabilities): bool
    {
        $roleObj = get_role($role);

        if (!$roleObj) {
            return false;
        }

        foreach ((array)$capabilities as $cap) {
            $roleObj->add_cap($cap);
        }

        return true;
    }

    /**
     * Remove one or more capabilities from a role.
     *
     * @param string $role Role name.
     * @param string|array<int, string> $capabilities Capability or list of capabilities.
     * @return bool True if the role exists and capabilities were removed.
     */
    public static function removeFromRole(string $role, string|array $capabilities): bool
    {
        $roleObj = get_role($role);

        if (!$roleObj) {
            return false;
        }

        foreach ((array)$capabilities as $cap) {
            $roleObj->remove_cap($cap);
        }

        return true;
    }
}
