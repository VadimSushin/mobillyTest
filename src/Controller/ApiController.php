<?php

namespace App\Controller;

use App\Services\BearerAuthenticationService;
use App\Services\DatasourceConnectorService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ApiController extends AbstractController
{
    /** @var DatasourceConnectorService $sourceApi */
    private $sourceApi;

    /** @var BearerAuthenticationService $bearerAuthenticationService */
    private $bearerAuthenticationService;

    /**
     * @param DatasourceConnectorService $sourceApi
     * @param BearerAuthenticationService $bearerAuthenticationService
     */
    public function __construct(DatasourceConnectorService $sourceApi, BearerAuthenticationService $bearerAuthenticationService)
    {
        $this->sourceApi = $sourceApi;
        $this->bearerAuthenticationService = $bearerAuthenticationService;
    }

    /**
     * @return JsonResponse
     */
    public function list(Request $request)
    {
        if (!$this->bearerAuthenticationService->authenticate($request)) {
            return $this->unauthorized();
        }

        $list = $this->sourceApi->getList();
        if (!$list['success']) {
            //datasource error
            return new JsonResponse(['success' => false, 'Error' => $list['error']], 200);
        }
        $result = [];
        foreach ($list['result']['records'] as $record) {
            $result[] = [
                'station_id' => $record['STATION_ID'],
                'name' => $record['NAME']
            ];
        }
        return new JsonResponse(['success' => true, 'result' => $result]);
    }

    /**
     * @param $id
     * @param Request $request
     * @return JsonResponse
     */
    public function details($id, Request $request)
    {
        if (!$this->bearerAuthenticationService->authenticate($request)) {
            return $this->unauthorized();
        }

        // First try to get ID as a part of URL like http://mobilly_test.local/details/123
        if (is_null($id) && $request->query->has('id')) {
            // If not success, try standard GET notation: http://mobilly_test.local/details?id=123
            $id = $request->query->get('id');
        }
        if (is_null($id)) {
            // No station ID provided
            return new JsonResponse(
                [
                    'success' => false,
                    'Error' => [
                        'message' => 'No station Id in request.',
                        '__type' => 'Wrong request'
                    ]
                ],
                404
            );
        }
        $details = $this->sourceApi->getDetails($id);

        if (!$details['success']) {
            //data source error
            return new JsonResponse(['success' => false, 'Error' => $details['error']], 200);
        } elseif ($details['result']['total'] > 1 || $details['result']['records'][0]['STATION_ID'] !== $id) {
            //multiple results received or $id is wrong
            return new JsonResponse(
                [
                    'success' => false,
                    'Error' => [
                        'message' => 'Nothing found.',
                        '__type' => 'Not Found Error'
                    ]
                ],
                200
            );
        } elseif ($details['result']['total'] === 0) {
            //nothing found
            return new JsonResponse(
                [
                    'success' => false,
                    'Error' => [
                        'message' => 'Nothing found.',
                        '__type' => 'Not Found Error'
                    ]
                ],
                404
            );
        }
        return new JsonResponse(['success' => true, 'result' => $details['result']['records'][0]]);
    }

    /**
     * @return JsonResponse
     */
    private function unauthorized()
    {
        return new JsonResponse(
            [
                'success' => false,
                'Error' => [
                    'message' => 'You are not authorized to use this service.',
                    '__type' => 'Not authorized'
                ]
            ],
            401
        );
    }
}