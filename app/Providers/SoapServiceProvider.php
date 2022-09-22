<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class SoapServiceProvider extends ServiceProvider
{
    /**
     * It creates a new SoapClient instance with the given WSDL and endpoint
     *
     * @param string ws The name of the web service you want to connect to.
     *
     * @return A SoapClient object.
     */
    public function createSoapClient(string $ws)
    {
        /* Getting the server and user token from the config file. */
        $server = config('services.soap.server');

        $userToken = config('services.soap.token');

        //URL to download a remote WSDL

        $wsdl = "https://$server/se/ws/$ws.php?wsdl";

        /* endpoint to connect. */

        $location = "https://$server/katana/apigateway/se/ws/$ws.php";

        return new \SoapClient($wsdl, [
            'trace' => 1 /* enable trace */,

            'exceptions' => 1 /* enable exceptions */,

            'stream_context' => stream_context_create([
                'http' => [
                    'header' => 'Authorization: ' . $userToken,
                ],

                'ssl' => [
                    'verify_peer' => false,

                    'verify_peer_name' => false,
                ],
            ]),

            'location' => $location,
        ]);
    }

    /**
     * Register services.
     * The function creates a singleton instance of the SoapClient class for each of the two WSDLs
     * @return void
     *
     */
    public function register()
    {
        $this->app->singleton('soap_client_wf_ws', function () {
            return $this->createSoapClient('wf_ws');
        });

        $this->app->singleton('soap_client_fm_ws', function () {
            return $this->createSoapClient('fm_ws');
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
