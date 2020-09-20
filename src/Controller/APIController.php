<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class APIController extends AbstractController
{
    /***************** METHODES D'APPEL ****************/

    /**
     * Generic Create Entity API Endpoint
     * @Route("/api/{entity}", name="api_create", methods={"POST"})
     *
     * @param Request $request
     * @param string $entity
     * @return JsonResponse
     */
    public function apiCreate(Request $request, string $entity) : JsonResponse {

    }

    /**
     * Generic Read Entity API Endpoint
     * @Route("/api/{entity}", name="api_read", methods={"GET"})
     *
     * @param Request $request
     * @param string $entity
     * @return JsonResponse
     */
    public function apiRead(Request $request, string $entity) : JsonResponse {

    }

    /**
     * Generic Update Entity API Endpoint
     * @Route("/api/{entity}/{id}", name="api_update", methods={"PUT"})
     *
     * @param Request $request
     * @param string $entity
     * @param int $id
     * @return JsonResponse
     */
    public function apiUpdate(Request $request, string $entity, int $id) : JsonResponse {

    }

    /**
     * Generic Delete Entity API Endpoint
     * @Route("/api/{entity}/{id}", name="api_delete", methods={"DELETE"})
     *
     * @param Request $request
     * @param string $entity
     * @return JsonResponse
     */
    public function apiDelete(Request $request, string $entity, int $id) : JsonResponse {


    }

    /***************** METHODES SECONDAIRES ****************/

    private function getEntityRepo(string $entityName) {

    }


}
