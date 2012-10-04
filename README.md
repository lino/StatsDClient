StatsD Client
==========

Small PHP client which pushes data over UDP to a stats.d server.
Feature suggestions and feedback is always welcome.

# Installation

1. [Add this client to the dependencies](#add-this-client-to-the-dependencies)
2. [Configure StatsD](#configure-statsd)
3. [Register namespace](#register-namespace)
4. [Register StatsD as a service](#register-statsd-as-a-service)

## Add this client to the dependencies
### Symfony 2.0

Add the following lines to your `deps`-file.

```
[StatsDClient]
    git=git://github.com/lino-dp/StatsDClient.git
    target=/statsd
```

Now, run the vendors script to download the client:

``` bash
php bin/vendors install
```

### Symfony 2.1

As this Client hasn't yet found its way into packagist you will have to register it as repository first.
Register the git-repository by adding the following lines to your `composer.json`.

``` json
{
    "repositories": [
        {
            "type": "git",
            "url": "https://github.com/lino-dp/StatsDClient.git"
        }
    ],
    "require": {
        "php": "DigitalPioneers/StatsD-Client",
    }
}
```

Now, run the vendors script to download the client:

``` bash
php composer.phar install
```

## Configure StatsD

The Client needs to know where your StatsD is running. Add the following lines to your `parameters.ini`.

```
statsd.host = "<IP or Host of your stats.d Server, default 127.0.0.1>"
statsd.port = "<Port of your stats.d Server, default 8125>"
```

## Register namespace
### Symfony 2.0

Register the namespace StatsD in `app/autoload.php`.

``` php
$loader->registerNamespaces(
    array(
    	/// (...)
        'StatsD'                         => SYMFONY_VENDOR_PATH . '/statsd/src',
    	/// (...)
    ));
```

### Symfony 2.1

Composer should've registered the namespace properly.

## Register StatsD as a service

Now add the following lines to your `config.yml` and you are good to go.

```
services:
    statsd:
        class: StatsD\StatsD
        arguments: [%statsd.host%, %statsd.port%]
```

# Usage

The StatsD Client is accessible as a service through the container. It offers various methods to manipulate your statistics.

Take a look at the following example to understand how to use the Client.

``` php
$statsd = $this->get('statsd');
$statsd->increment('mypage.visits');
```

For further informations on the functions offered take a look at the [StatsD.php](https://github.com/lino-dp/StatsDClient/blob/master/src/StatsD/StatsD.php).
