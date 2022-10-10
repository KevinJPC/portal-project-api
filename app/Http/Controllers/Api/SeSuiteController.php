<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\Controller;

class SeSuiteController extends Controller
{
    private $formsWebService;

    public function __construct()
    {
        $this->formsWebService = app('soap_client_fm_ws');
    }

    /**
     * A test function to see what functions are available in the web service.
     */
    public function testWs()
    {
        dd($this->formsWebService->__getFunctions());

        // $response = $this->soapClient->getWorkflow([
        //     'WorkflowID' => '000023',
        // ]);

        // dd($response);
    }
}
