StatsD Lib
==========

Small client which pushes data over UDP to stats.d Server
Feature suggestions and feedback is always welcome.

Installation
============


Step 1: Append the following lines to the respective Files

deps
----
[StatsDClient]
    git=git://github.com/lino-dp/StatsDClient.git
    target=/statsd

config.yml (If the sections are already defined, add the parameters, do not add the sections again)
---------------------------------------------------------------------------------------------------
parameters:
    statsd.host:   <<<IP or Host of your stats.d Server, default 127.0.0.1>>>
    statsd.port:   <<<Port of your stats.d Server, default 8125>>>

services:
    statsd:
        class:     StatsD\StatsD
        arguments: [%statsd.host%, %statsd.port%]


Step 2: Now register the namespace StatsD in app/autoload.php, run bin/vendors install and you are done.