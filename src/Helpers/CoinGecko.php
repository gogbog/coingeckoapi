<?php

namespace CoinGecko\Helpers;

use GuzzleHttp\Client;

class CoinGecko {

    private $client;

    public static $API_ROUTE = 'https://api.coingecko.com/api/v3/';
    public static $TIMEOUT = 5.0;

    //Options routes
    public static $TEST_ROUTE = 'ping';
    public static $COIN_LIST_ROUTE = 'coins/list';
    public static $COINS_MARKETS_ROUTE = 'coins/markets';
    public static $SIMPLE_PRICE_ROUTE = 'simple/price';
    public static $SUPPORTED_VS_CURRENCIES = 'simple/supported_vs_currencies';
    public static $EXCHANGES_ROUTE = 'exchanges';
    public static $EXCHANGES_LIST_ROUTE = 'exchanges/list';
    private static $EVENTS_ROUTE = 'events';
    private static $COUNTRIES_ROUTE = 'events/countries';
    private static $EVENT_TYPES_ROUTE = 'events/types';
    private static $EXCHANGE_RATES_ROUTE = 'exchange_rates';
    private static $GLOBAL_ROUTE = 'global';



    //Errors
    private static $URL_NOT_FOUND = 'URL - Not found';
    private static $NOT_ARRAY = 'Options should be array';

    public function __construct() {
        $this->client = new Client([
            // Base URI is used with relative requests
            'base_uri' => self::$API_ROUTE,
            // You can set any number of default request options.
            'timeout' => self::$TIMEOUT,
        ]);
    }

    /**
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function ping() {
        $response = $this->send(self::$TEST_ROUTE);
        return $this->beautifyUrl($response->getBody());
    }

    //TODO Token price

    /**
     * @param $ids
     * @param $vs_currencies
     * @param array $options
     *
     * @$options
     * -- ids = crypto currencies ids (required)
     * -- vs_ids = supported currencies ids (required)
     * -- include_market_cap = true/false (string)
     * -- include_24hr_vol = true/false (string)
     * -- include_24hr_change = true/false (string)
     * -- include_last_updated_at = true/false (string)
     *
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function simplePrice($ids, $vs_currencies, $options = []) {
        $options['ids'] = $ids;
        $options['vs_currencies'] = $vs_currencies;

        $route = $this->beautifyRoute(self::$SIMPLE_PRICE_ROUTE, $options);
        $response = $this->send($route);
        return $this->beautifyUrl($response->getBody());
    }


    /**
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function supportedVsCurrencies() {
        $response = $this->send(self::$SUPPORTED_VS_CURRENCIES);
        return $this->beautifyUrl($response->getBody());
    }


    /**
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function coinList() {
        $response = $this->send(self::$COIN_LIST_ROUTE);
        return $this->beautifyUrl($response->getBody());
    }


    /**
     * @param $vs_currency
     * @param array $options
     *
     * @$options
     * -- $vs_currency = supported currencies id (required)
     * -- ids = crypto currencies ids (string)
     * -- order = market_cap_desc, gecko_desc, gecko_asc, market_cap_asc, market_cap_desc, volume_asc, volume_desc (string)
     * -- per_page = 1-250 (string)
     * -- page = number of the page (integer)
     * -- sparkline = returns array sparkline in wast 7 days (true/false)
     * -- price_change_percentage = 1h, 24h, 7d, 14d, 30d, 200d, 1y (string)
     *
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Exception
     */
    public function coinMarkets($vs_currency, $options = []) {
        $options['vs_currency'] = $vs_currency;

        $route = $this->beautifyRoute(self::$COINS_MARKETS_ROUTE, $options);
        $response = $this->send($route);
        return $this->beautifyUrl($response->getBody());
    }

    /**
     * @param $id
     * @param array $options
     * @$options
     * -- id = crypto currencies id (required)
     * -- localization = Include all localized languages in response true/false (string)
     * -- tickers = true/false (string)
     * -- market_data = true/false (string)
     * -- community_data = true/false (string)
     * -- developer_data = true/false (string)
     * -- sparkline = true/false (string)
     *
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function coin($id, $options = []) {
        $route = 'coins/' . $id;
        $route = $this->beautifyRoute($route, $options);
        $response = $this->send($route);
        return $this->beautifyUrl($response->getBody());
    }


    /**
     * @param $id
     * @param array $options
     * @$options
     * -- id = crypto currencies id (required)
     * -- exchange_ids = filter results by exchange_ids (string)
     * -- page = number of the page (integer)
     *
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function coinTickers($id, $options = []) {
        $route = 'coins/' . $id . '/tickers';
        $route = $this->beautifyRoute($route, $options);
        $response = $this->send($route);
        return $this->beautifyUrl($response->getBody());
    }

    /**
     * @param $id
     * @param $date
     * @param array $options
     * @$options
     * -- id = crypto currencies id (required)
     * -- date = dd-mm-yyyy(required)
     * -- localization = Include all localized languages in response true/false (string)
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function coinHistory($id, $date, $options = []) {
        $options['date'] = $date;
        $route = 'coins/' . $id . '/history';
        $route = $this->beautifyRoute($route, $options);
        $response = $this->send($route);
        return $this->beautifyUrl($response->getBody());
    }


    /**
     * @param $id
     * @param $vs_currency
     * @param $days
     * @param array $options
     * @$options
     * -- id = crypto currencies id (required)
     * -- vs_currency = The target currency of market data (usd, eur, jpy, etc.) (required)
     * -- days = Data up to number of days ago (eg. 1,14,30,max) (required)
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function marketChart($id, $vs_currency, $days, $options = []) {
        $options['vs_currency'] = $vs_currency;
        $options['days'] = $days;
        $route = 'coins/' . $id . '/market_chart';
        $route = $this->beautifyRoute($route, $options);
        $response = $this->send($route);
        return $this->beautifyUrl($response->getBody());
    }


    /**
     * @param $id
     * @param array $options
     * @$options
     * -- id = crypto currencies id (required)
     * -- per_page = 1-250 (string)
     * -- page = number of the page (integer)
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function coinStatusUpdates($id, $options = []) {
        $route = 'coins/' . $id . '/status_updates';
        $route = $this->beautifyRoute($route, $options);
        $response = $this->send($route);
        return $this->beautifyUrl($response->getBody());
    }


    /**
     * @param $id
     * @param $contact_address
     * @param array $options
     * @$options
     * -- id = crypto currencies id (required)
     * -- contract_address = Token’s contract address (required)
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function contract($id, $contact_address, $options = []) {
        $route = 'coins/' . $id . '/contract/' . $contact_address;
        $route = $this->beautifyRoute($route, $options);
        $response = $this->send($route);
        return $this->beautifyUrl($response->getBody());
    }


    /**
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Exception
     */
    public function exchanges() {
        $route = $this->beautifyRoute(self::$EXCHANGES_ROUTE);
        $response = $this->send($route);
        return $this->beautifyUrl($response->getBody());
    }

    /**
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function exchangesList() {
        $route = $this->beautifyRoute(self::$EXCHANGES_LIST_ROUTE);
        $response = $this->send($route);
        return $this->beautifyUrl($response->getBody());
    }

    /**
     * @param $id
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function exchange($id) {
        $route = 'exchanges/' . $id;
        $route = $this->beautifyRoute($route);
        $response = $this->send($route);
        return $this->beautifyUrl($response->getBody());
    }

    /**
     * @param $id
     * @param array $options
     * @$options
     * -- id = exchange id (required)
     * -- coins_ids = filter tickers by coin_ids (ref: v3/coins/list)
     * -- page = number of the page (integer)
     *
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function exchangesTickers($id, $options = []) {
        $route = 'exchanges/' . $id . '/tickers';
        $route = $this->beautifyRoute($route, $options);
        $response = $this->send($route);
        return $this->beautifyUrl($response->getBody());
    }

    /**
     * @param $id
     * @param array $options
     * @$options
     * -- id = exchange id (required)
     * -- per_page = 1-250 (string)
     * -- page = number of the page (integer)
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function exchangeStatusUpdate($id, $options = []) {
        $route = 'exchanges/' . $id . '/status_updates';
        $route = $this->beautifyRoute($route, $options);
        $response = $this->send($route);
        return $this->beautifyUrl($response->getBody());
    }

    /**
     * @param array $options
     * @$options
     * -- country_code = country_code of event (eg. ‘US’). use /api/v3/events/countries for list of country_codes
     * -- type = type of event (eg. ‘Conference’). use /api/v3/events/types for list of types
     * -- page = number of the page (integer)
     * -- upcoming_events_only = lists only upcoming events. (true/false)
     * -- from_date = lists events after this date yyyy-mm-dd
     * -- to_date = lists events before this date yyyy-mm-dd (set upcoming_events_only to false if fetching past events)
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function events($options = []) {
        $route = $this->beautifyRoute(self::$EVENTS_ROUTE, $options);
        $response = $this->send($route);
        return $this->beautifyUrl($response->getBody());
    }

    /**
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function eventCountries() {
        $route = $this->beautifyRoute(self::$COUNTRIES_ROUTE);
        $response = $this->send($route);
        return $this->beautifyUrl($response->getBody());
    }

    /**
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function eventTypes() {
        $route = $this->beautifyRoute(self::$EVENT_TYPES_ROUTE);
        $response = $this->send($route);
        return $this->beautifyUrl($response->getBody());
    }

    /**
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function exchangeRates() {
        $route = $this->beautifyRoute(self::$EXCHANGE_RATES_ROUTE);
        $response = $this->send($route);
        return $this->beautifyUrl($response->getBody());
    }

    /**
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function global() {
        $route = $this->beautifyRoute(self::$GLOBAL_ROUTE);
        $response = $this->send($route);
        return $this->beautifyUrl($response->getBody());
    }






    /**
     * @param string $url
     * @return mixed|\Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function send($url = '/') {
        return $this->client->request('GET', $url);
    }

    private function beautifyUrl($response): array {
        return (array) json_decode($response);
    }

    private function beautifyRoute($url, $options = []) {
        $route = $url;

        if (!empty($options) && !is_array($options)) {
            throw new \Exception(self::$NOT_ARRAY);
        } else {
            $route .= '?';
        }

        foreach ($options as $option_key => $option_value) {
            if (is_array($option_value)) {
                $array_to_string = '';

                foreach ($option_value as $value) {
                    $array_to_string .= $value;

                    if (next($option_value)) {
                        $array_to_string .= ',';
                    }
                }
                $option_value = $array_to_string;
            }
            $route .= $option_key . '=' . $option_value;

            if (next($options)) {
                $route .= '&';
            }
        }

        return $route;
    }
}