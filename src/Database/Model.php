<?php

declare(strict_types=1);

namespace WPJarvis\Core\Database;

use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Support\Collection;
use WP_Post;
use WP_User;
use WP_Comment;
use WP_Term;

/**
 * Class Model
 *
 * A Laravel-style model class with extended support for WordPress data structures.
 */
class Model extends EloquentModel
{
    /**
     * The WordPress table prefix.
     *
     * @var string
     */
    protected string $prefix;

    /**
     * Create a new model instance.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        global $wpdb;
        $this->prefix = $wpdb->prefix;

        parent::__construct($attributes);
    }

    /**
     * Get the full table name with prefix.
     *
     * @return string
     */
    public function getTable(): string
    {
        if (!isset($this->table)) {
            return $this->prefix . parent::getTable();
        }

        return str_starts_with($this->table, $this->prefix)
            ? $this->table
            : $this->prefix . $this->table;
    }

    /**
     * Get a WordPress DB connection instance.
     *
     * @return \Illuminate\Database\Connection
     */
    public function getConnection()
    {
        return app('db')->connection();
    }

    /**
     * Populate model from WP_Post.
     *
     * @param int|WP_Post $post
     * @return static|null
     */
    public static function fromPost(int|WP_Post $post): ?static
    {
        $post = is_numeric($post) ? get_post($post) : $post;

        return $post ? new static((array) $post) : null;
    }

    /**
     * Get multiple posts as models.
     *
     * @param array $args
     * @return Collection<int, static>
     */
    public static function fromPosts(array $args = []): Collection
    {
        $args = array_merge([
            'post_type' => 'post',
            'posts_per_page' => -1,
            'post_status' => 'publish',
        ], $args);

        $posts = get_posts($args);

        return collect($posts)->map(fn($post) => new static((array) $post));
    }

    /**
     * Create model from WP_Term.
     *
     * @param int|WP_Term $term
     * @param string|null $taxonomy
     * @return static|null
     */
    public static function fromTerm(int|WP_Term $term, ?string $taxonomy = null): ?static
    {
        if (is_numeric($term) && $taxonomy) {
            $term = get_term($term, $taxonomy);
        }

        return ($term && !is_wp_error($term)) ? new static((array) $term) : null;
    }

    /**
     * Load terms as models.
     *
     * @param array $args
     * @return Collection<int, static>
     */
    public static function fromTerms(array $args = []): Collection
    {
        $args = array_merge([
            'taxonomy' => 'category',
            'hide_empty' => false,
        ], $args);

        $terms = get_terms($args);

        if (is_wp_error($terms)) {
            return collect();
        }

        return collect($terms)->map(fn($term) => new static((array) $term));
    }

    /**
     * Load a model from a WP_User.
     *
     * @param int|WP_User $user
     * @return static|null
     */
    public static function fromUser(int|WP_User $user): ?static
    {
        $user = is_numeric($user) ? get_user_by('id', $user) : $user;

        return $user ? new static((array) $user->data) : null;
    }

    /**
     * Load multiple users as models.
     *
     * @param array $args
     * @return Collection<int, static>
     */
    public static function fromUsers(array $args = []): Collection
    {
        $args = array_merge(['fields' => 'all'], $args);
        $users = get_users($args);

        return collect($users)->map(fn($user) => new static((array) $user->data));
    }

    /**
     * Load a model from a WP_Comment.
     *
     * @param int|WP_Comment $comment
     * @return static|null
     */
    public static function fromComment(int|WP_Comment $comment): ?static
    {
        $comment = is_numeric($comment) ? get_comment($comment) : $comment;

        return $comment ? new static((array) $comment) : null;
    }

    /**
     * Load multiple comments as models.
     *
     * @param array $args
     * @return Collection<int, static>
     */
    public static function fromComments(array $args = []): Collection
    {
        $args = array_merge(['number' => 10], $args);
        $comments = get_comments($args);

        return collect($comments)->map(fn($comment) => new static((array) $comment));
    }

    /**
     * Load model by meta key & value.
     *
     * @param string $metaKey
     * @param mixed $value
     * @param string $objectType
     * @return Collection<int, static>
     */
    public static function fromMeta(string $metaKey, mixed $value, string $objectType = 'post'): Collection
    {
        $args = [
            'meta_query' => [
                [
                    'key' => $metaKey,
                    'value' => $value,
                    'compare' => '='
                ]
            ]
        ];

        return match ($objectType) {
            'post' => static::fromPosts($args),
            'user' => static::fromUsers($args),
            'term' => static::fromTerms($args),
            'comment' => static::fromComments($args),
            default => collect(),
        };
    }

    /**
     * Sync model attributes from array (fillable-only).
     *
     * @param array $data
     * @return void
     */
    public function syncFromArray(array $data): void
    {
        foreach ($data as $key => $value) {
            if (in_array($key, $this->fillable, true)) {
                $this->setAttribute($key, $value);
            }
        }
    }

    /**
     * Convert the model back to a WP_Post.
     *
     * @return WP_Post|null
     */
    public function toPost(): ?WP_Post
    {
        return get_post($this->getAttribute('ID') ?? 0);
    }

    /**
     * Convert the model back to WP_User.
     *
     * @return WP_User|null
     */
    public function toUser(): ?WP_User
    {
        return get_user_by('id', $this->getAttribute('ID') ?? 0);
    }
}
