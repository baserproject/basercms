# OAuth client class for CakePHP

## Purpose

An OAuth client class for CakePHP 2.x supporting OAuth 1.0 as defined in http://tools.ietf.org/html/rfc5849. For CakePHP 1.x, please checkout the [`cakephp_1.x` branch](https://github.com/cakebaker/oauth-consumer/tree/cakephp_1.x) and/or [download](https://github.com/cakebaker/oauth-consumer/zipball/v1.0.1) the latest version for it.

## Installation

* Place all files into an `OAuth` folder in the `Vendor` folder of your application

## Usage

To use the OAuth client class, you have to import it with `App::import()`.

Before you can instantiate the client class, you have to register your application with your API provider to get consumer key and consumer secret (for this example you have to register your application at https://twitter.com/oauth). Consumer key and consumer secret are required as parameters for the constructor. In the example below I moved the instantiation of the client class to a private method `createClient()` to avoid code duplication.

In the `index` method a request token is obtained and the user is redirected to Twitter to authorize the request token.

In the `callback` method the request token is exchanged for an access token. Using this access token, a new status is posted to Twitter. Please note that in a real application, you would save the access token data in a database to avoid that the user has to go through the process of getting an access token over and over again.

```php
<?php
// Controller/TwitterController.php
App::import('Vendor', 'OAuth/OAuthClient');

class TwitterController extends AppController {
  public function index() {
    $client = $this->createClient();
    $requestToken = $client->getRequestToken('https://api.twitter.com/oauth/request_token', 'http://' . $_SERVER['HTTP_HOST'] . '/twitter/callback');

    if ($requestToken) {
      $this->Session->write('twitter_request_token', $requestToken);
      $this->redirect('https://api.twitter.com/oauth/authorize?oauth_token=' . $requestToken->key);
    } else {
      // an error occured when obtaining a request token
    }
  }

  public function callback() {
    $requestToken = $this->Session->read('twitter_request_token');
    $client = $this->createClient();
    $accessToken = $client->getAccessToken('https://api.twitter.com/oauth/access_token', $requestToken);

    if ($accessToken) {
      $client->post($accessToken->key, $accessToken->secret, 'https://api.twitter.com/1/statuses/update.json', array('status' => 'hello world!'));
    }
    exit;
  }

  private function createClient() {
    return new OAuthClient('YOUR_CONSUMER_KEY', 'YOUR_CONSUMER_SECRET');
  }
}
```

## Migration from CakePHP 1.x to CakePHP 2.x

If you are migrating your application to CakePHP 2.x, you have to make a few changes beside updating the client class and the OAuth library. First, you have to change `App::import('Vendor', 'oauth', array('file' => 'OAuth'.DS.'oauth_consumer.php'));` to `App::import('Vendor', 'OAuth/OAuthClient');`. And second, you have to rename the class from `OAuth_Consumer` to `OAuthClient` when instantiating it.

## Contact

If you have questions or feedback, feel free to contact me via Twitter ([@dhofstet](https://twitter.com/dhofstet)) or by email (daniel.hofstetter@42dh.com).

## License

The OAuth client class is licensed under the MIT license.
