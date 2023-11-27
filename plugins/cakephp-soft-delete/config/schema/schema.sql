-- sqlite schema.
CREATE TABLE `users` (
    `id` INTEGER PRIMARY KEY AUTOINCREMENT,
    `posts_count` integer NOT NULL DEFAULT 0,
    `deleted` datetime DEFAULT NULL
);

CREATE TABLE `tags` (
    `id` INTEGER PRIMARY KEY AUTOINCREMENT,
    `name` text DEFAULT NULL,
    `deleted_date` datetime DEFAULT NULL
);

CREATE TABLE `posts` (
    `id` INTEGER PRIMARY KEY AUTOINCREMENT,
    `user_id` integer NOT NULL DEFAULT 0,
    `deleted` datetime DEFAULT NULL
);
CREATE INDEX `posts_user_id_idx` ON `posts`(`user_id`);

CREATE TABLE `posts_tags` (
    `id` INTEGER PRIMARY KEY AUTOINCREMENT,
    `post_id` integer DEFAULT NULL,
    `tag_id` integer DEFAULT NULL,
    `deleted` datetime DEFAULT NULL
);
CREATE INDEX `posts_tags_post_id_idx` ON `posts_tags`(`post_id`);
CREATE INDEX `posts_tags_tag_id_idx` ON `posts_tags`(`tag_id`);