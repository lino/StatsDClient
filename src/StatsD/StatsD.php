<?php

namespace StatsD;

/**
 * Forked from:
 *     git://gist.github.com/1065177.git gist-1065177
 * See:
 *     https://gist.github.com/1065177/5f7debc212724111f9f500733c626416f9f54ee6
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
class StatsD {

    private $host;
    private $port;

    public function __construct($host="127.0.0.1", $port=8125) {
        $this->host = $host;
        $this->port = $port;
    }

    /**
     * Log timing information
     *
     * @param $stat $stats The metric to in log timing info for.
     * @param $time $time The elapsed time (ms) to log
     * @param $sampleRate|1 $sampleRate the rate (0-1) for sampling.
     **/
    public function timing($stat, $time, $sampleRate=1) {
        $this->send(array($stat => "$time|ms"), $sampleRate);
    }

    /**
     * Log Gauges, arbitrary values, which can be recorded.
     *
     * @param string $stats The metric to in log gauge info for.
     * @param double $time The arbitrary value to log
     * @param float|1 $sampleRate the rate (0-1) for sampling.
     **/
    public function gauges($stat, $gauge, $sampleRate=1) {
        $this->send(array($stat => "$gauge|g"), $sampleRate);
    }

    /**
     * Increments one or more stats counters
     *
     * @param string|array $stats The metric(s) to increment.
     * @param float|1 $sampleRate the rate (0-1) for sampling.
     * @return boolean
     **/
    public function increment($stats, $sampleRate=1) {
        $this->updateStats($stats, 1, $sampleRate);
    }

    /**
     * Decrements one or more stats counters.
     *
     * @param string|array $stats The metric(s) to decrement.
     * @param float|1 $sampleRate the rate (0-1) for sampling.
     * @return boolean
     **/
    public function decrement($stats, $sampleRate=1) {
        $this->updateStats($stats, -1, $sampleRate);
    }

    /**
     * Updates one or more stats counters by arbitrary amounts.
     *
     * @param string|array $stats The metric(s) to update. Should be either a string or array of metrics.
     * @param int|1 $delta The amount to increment/decrement each metric by.
     * @param float|1 $sampleRate the rate (0-1) for sampling.
     * @return boolean
     **/
    public function updateStats($stats, $delta=1, $sampleRate=1) {
        if (!is_array($stats)) { $stats = array($stats); }
        $data = array();
        foreach($stats as $stat) {
            $data[$stat] = "$delta|c";
        }

        $this->send($data, $sampleRate);
    }

    /*
     * Squirt the metrics over UDP
     **/
    public function send($data, $sampleRate=1) {

        // sampling
        $sampledData = array();

        if ($sampleRate < 1) {
            foreach ($data as $stat => $value) {
                if ((mt_rand() / mt_getrandmax()) <= $sampleRate) {
                    $sampledData[$stat] = "$value|@$sampleRate";
                }
            }
        } else {
            $sampledData = $data;
        }

        if (empty($sampledData)) { return; }

        try{
            $fp = fsockopen("udp://" . $this->host, $this->port, $errno, $errstr);
            if (! $fp) { return; }
            foreach ($sampledData as $stat => $value) {
                fwrite($fp, "$stat:$value");
            }
            fclose($fp);
        }catch(Exception $e){
            // do nothing
        }
    }
}
