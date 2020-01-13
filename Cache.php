<?php


namespace esportz\addons\abios;

use SQLite3;

class Cache
{
    /**
     * @var SQLite3
     */
    private $connection;

    /**
     * @var
     */
    public $timezone;

    function __construct()
    {
        $this->connection = $this->connect();
        $this->timezone = esportz_timezone_string();
        date_default_timezone_set($this->timezone);
    }

    /**
     * @return SQLite3
     */
    private function connect()
    {
        $options = esportz_get_theme_options();

        $dbName = $options['databasePath'];

        return new SQLite3($dbName);
    }

    /**
     * @return mixed|null
     * @throws \Exception
     */
    public function clearCache()
    {

        $this->connection->query("DELETE FROM cache WHERE strftime('%s', 'now') > valid_until_unix;");
        $this->connection->query("VACUUM");

    }


    /**
     * @param $url
     * @return mixed|null
     * @throws \Exception
     */
    public function getFromCacheByUrl($url)
    {

        $url = substr($url, 0, strpos($url, "access_token"));

        $curTime = new \DateTime();
        $timezone = new \DateTimeZone('America/Los_Angeles');
        $curTime->setTimezone($timezone);
        $curTimeUnix = $curTime->getTimestamp();

        $statement = $this->connection->query("SELECT data FROM cache WHERE url = '$url' AND valid_until_unix > $curTimeUnix;");

        $result = $statement->fetchArray();

        if ($result) {

            return json_decode($result[0], true);
        }

        return null;

    }

    /**
     * @param $url
     * @param $response
     * @param $validityPeriodInHours
     * @throws \Exception
     */
    public function saveToCache($url, $response, $validityPeriodInHours)
    {

        $url = substr($url, 0, strpos($url, "access_token"));

        $result = $this->connection->query("SELECT cache_id FROM cache WHERE url = '$url';");

        $encodedResponse = json_encode($response);

        $validUntilDate = new \DateTime();
        $timezone = new \DateTimeZone('America/Los_Angeles');
        $validUntilDate->setTimezone($timezone);
        $validUntilDate->modify('+'.$validityPeriodInHours.' hours');
        $validUntilDateUnix = $validUntilDate->getTimestamp();
        $validUntilDateString = $validUntilDate->format('Y-m-d H:i:s');

        $encodedResponse = \SQLite3::escapeString($encodedResponse);

        try {
            if ($result->fetchArray()) {
                $statement = $this->connection->prepare(
                    "UPDATE cache SET data = '$encodedResponse', valid_until_unix = $validUntilDateUnix, valid_until = '$validUntilDateString' WHERE url = '$url';"
                );
            } else {
                $statement = $this->connection->prepare(
                    "INSERT INTO cache (url, data, valid_until_unix, valid_until) VALUES ('$url', '$encodedResponse', $validUntilDateUnix,'$validUntilDateString');"
                );

            }

            $statement->execute();

        } catch (\Exception $ex) {
            throw new \Exception('Database insert or update failed');
        }
    }
}
