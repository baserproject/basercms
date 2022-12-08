<?php
/*
 * Local configuration file to provide any overrides to your app.php configuration.
 * Copy and save this file as app_local.php and make changes as required.
 * Note: It is not recommended to commit files with credentials such as app_local.php
 * into source code version control.
 */

use Cake\Cache\Engine\FileEngine;
use Cake\Log\Engine\FileLog;

return [
    /*
     * Debug Level:
     *
     * Production Mode:
     * false: No error messages, errors, or warnings shown.
     *
     * Development Mode:
     * true: Errors and warnings shown.
     */
    'debug' => filter_var(env('DEBUG', true), FILTER_VALIDATE_BOOLEAN),

    /*
     * Security and encryption configuration
     *
     * - salt - A random string used in security hashing methods.
     *   The salt value is also used as the encryption key.
     *   You should treat it as extremely sensitive data.
     */
    'Security' => [
        'salt' => env('SECURITY_SALT', 'af0aa6bb8bad3bf5f9a39e928c645745cc008d67d82ac4edd16ec17e99539725'),
    ],

    /*
     * Connection information used by the ORM to connect
     * to your application's datastores.
     *
     * See app.php for more configuration options.
     */
    'Datasources' => [
        'default' => [
            'host' => '',
            /*
             * CakePHP will use the default DB port based on the driver selected
             * MySQL on MAMP uses port 8889, MAMP users will want to uncomment
             * the following line and set the port accordingly
             */
            //'port' => 'non_standard_port_number',

            'username' => '',
            'password' => '',
            'database' => '',
            /*
             * If not using the default 'public' schema with the PostgreSQL driver
             * set it here.
             */
            //'schema' => 'myapp',

            /*
             * You can use a DSN string to set the entire configuration
             */
            'url' => env('DATABASE_URL', null),
            'log' => filter_var(env('SQL_LOG', false), FILTER_VALIDATE_BOOLEAN),
        ],

        /*
         * The test connection is used during the test suite.
         */
        'test' => [
            'host' => '',
            //'port' => 'non_standard_port_number',
            'username' => '',
            'password' => '',
            'database' => '',
            //'schema' => 'myapp',
            'url' => env('DATABASE_TEST_URL', null),
        ],
    ],

    /*
     * Email configuration.
     *
     * Host and credential configuration in case you are using SmtpTransport
     *
     * See app.php for more configuration options.
     */
    'EmailTransport' => [
        'default' => [
            'host' => 'localhost',
            'port' => 25,
            'username' => null,
            'password' => null,
            'client' => null,
            'url' => env('EMAIL_TRANSPORT_DEFAULT_URL', null),
        ],
    ],
    'Error' => [
        'errorLevel' => E_ALL & ~E_USER_DEPRECATED
    ],

    /*
     * Configure the cache adapters.
     */
    'Cache' => [
        '_bc_env_' => [
            'className' => FileEngine::class,
            'prefix' => 'myapp_bc_env_',
            'path' => CACHE . 'environment' . DS,
            'serialize' => true,
            'duration' => '+1 years',
            'url' => env('CACHE_BCENV_URL', null),
        ],
    ],

    /*
     * Configures logging options
     */
    'Log' => [
        'update' => [
            'className' => FileLog::class,
            'path' => LOGS,
            'file' => 'update',
            'scopes' => ['update'],
            'levels' => ['info', 'error']
        ]
    ],

    /*
     * Session configuration.
     *
     * Contains an array of settings to use for session configuration. The
     * `defaults` key is used to define a default preset to use for sessions, any
     * settings declared here will override the settings of the default config.
     *
     * ## Options
     *
     * - `cookie` - The name of the cookie to use. Defaults to value set for `session.name` php.ini config.
     *    Avoid using `.` in cookie names, as PHP will drop sessions from cookies with `.` in the name.
     * - `cookiePath` - The url path for which session cookie is set. Maps to the
     *   `session.cookie_path` php.ini config. Defaults to base path of app.
     * - `timeout` - The time in minutes the session should be valid for.
     *    Pass 0 to disable checking timeout.
     *    Please note that php.ini's session.gc_maxlifetime must be equal to or greater
     *    than the largest Session['timeout'] in all served websites for it to have the
     *    desired effect.
     * - `defaults` - The default configuration set to use as a basis for your session.
     *    There are four built-in options: php, cake, cache, database.
     * - `handler` - Can be used to enable a custom session handler. Expects an
     *    array with at least the `engine` key, being the name of the Session engine
     *    class to use for managing the session. CakePHP bundles the `CacheSession`
     *    and `DatabaseSession` engines.
     * - `ini` - An associative array of additional ini values to set.
     *
     * The built-in `defaults` options are:
     *
     * - 'php' - Uses settings defined in your php.ini.
     * - 'cake' - Saves session files in CakePHP's /tmp directory.
     * - 'database' - Uses CakePHP's database sessions.
     * - 'cache' - Use the Cache class to save sessions.
     *
     * To define a custom session handler, save it at src/Network/Session/<name>.php.
     * Make sure the class implements PHP's `SessionHandlerInterface` and set
     * Session.handler to <name>
     *
     * To use database sessions, load the SQL file located at config/schema/sessions.sql
     */
    'Session' => [
        'defaults' => 'cake',
        'cookie' => 'BASERCMS5',
        /**
         * セッションの有効期限（分）
         * デフォルト：2日間
         */
        'timeout' => 60 * 24 * 2,
        'ini' => [
            'session.serialize_handler' => 'php',
            'session.save_path' => TMP . 'sessions',
            'session.use_cookies' => 1,
            'session.use_trans_sid' => 0,
            'session.gc_divisor' => 1,
            'session.gc_probability' => 1,
        ]
    ],

];
