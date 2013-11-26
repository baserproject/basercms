# Changelog

### v2.1.1 (2012-08-02)

* Making `OAuth` the top level folder so that the project can be used as a Git submodule. Thanks to [Adam Duro](https://github.com/cakebaker/oauth-consumer/issues/1) for the suggestion!

### v2.1.0 (2012-06-15)

* Adding a `postMultidataFormData()` method to use for API methods like Twitter's [update_with_media](https://dev.twitter.com/docs/api/1/post/statuses/update_with_media) which expect multipart data

### v2.0.0 (2012-01-27)

* Adapting for CakePHP 2.x

### v1.0.1 (2012-01-27)

* Updating the OAuth library

### v1.0.0 (2011-08-24)

* Switching to Semantic Versioning
* Re-purpose the `getFullResponse()` method so it will always return an array with the complete response of the last request

### v2009-09-05

* Including the PHP library for OAuth in the package for convenience purposes, so you no longer have to download this library separately
* Adding a `getFullResponse()` method for debugging purposes
* Adapting the class for OAuth 1.0a. The `getRequestToken()` method got a new parameter named `$callback`: `getRequestToken($requestTokenURL, $callback = 'oob', $httpMethod = 'POST', $parameters = array())`

### v2009-03-30

* Initial release
