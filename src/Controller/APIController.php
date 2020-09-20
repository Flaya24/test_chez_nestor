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
        $repository = $this->getEntityRepo("App\\Entity\\" . ucfirst($entity));
        if(!$repository)
            throw $this->createNotFoundException('The entity does not exist');
        $data = json_decode($request->getContent(), true);

        // Create Process
        try {
            $new_entity = $repository->create($data);
            $errors = $repository->validate($new_entity);

            if(empty($errors)) {
                $repository->save($new_entity);

                if(is_array($new_entity))
                    return new JsonResponse(['new_item' => array_map(function($entity) { return $entity->toArray(); }, $new_entity) ]);
                else
                    return new JsonResponse(['new_item' => $new_entity->toArray() ]);
            }
            else {
                return new JsonResponse(['errors' => $errors ], 400);
            }
        }
        catch(\Exception $ex) {
            return new JsonResponse(['message' => $ex->getMessage() ], 500);
        }


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
        $repository = $this->getEntityRepo("App\\Entity\\" . ucfirst($entity));
        if(!$repository)
            throw $this->createNotFoundException('The entity does not exist');
        $data = $request->query->all();

        // Read Process
        if(!empty($data))
            $entities = $repository->findBy($data);
        else
            $entities = $repository->findAll();
        $formatted_entities = [];
        foreach($entities as $entity) {
            $formatted_entities[] = $entity->toArray();
        }

        return new JsonResponse(['items' => $formatted_entities]);
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
        $repository = $this->getEntityRepo("App\\Entity\\" . ucfirst($entity));
        if(!$repository)
            throw $this->createNotFoundException('The entity does not exist');
        $data = json_decode($request->getContent(), true);

        // Update Process
        try {
            $entity_to_update = $repository->findOneBy([ 'id' => $id ]);
            if(!is_object($entity_to_update))
                throw $this->createNotFoundException('The entity does not exist');

            $updated_entity = $repository->update($id, $data);
            $errors = $repository->validate($updated_entity, true);

            if(empty($errors)) {
                $repository->save($updated_entity);

                if(is_array($updated_entity))
                    return new JsonResponse(['updated_item' => array_map(function($entity) { return $entity->toArray(); }, $updated_entity) ]);
                else
                    return new JsonResponse(['updated_item' => $updated_entity->toArray() ]);
            }
            else {
                return new JsonResponse(['errors' => $errors ], 400);
            }
        }
        catch(\Exception $ex) {
            return new JsonResponse(['message' => $ex->getMessage() ], 500);
        }
    }

    /**
     * Generic Delete Entity API Endpoint
     * @Route("/api/{entity}/{id}", name="api_delete", methods={"DELETE"})
     *
     * @param Request $request
     * @param string $entity
     * @param int $id
     * @return JsonResponse
     */
    public function apiDelete(Request $request, string $entity, int $id) : JsonResponse {
        $repository = $this->getEntityRepo("App\\Entity\\" . ucfirst($entity));
        if(!$repository)
            throw $this->createNotFoundException('The entity does not exist');
        $data = json_decode($request->getContent(), true);

        // Requête
        try {
            $entity_to_delete = $repository->findOneBy([ 'id' => $id ]);
            if(!is_object($entity_to_delete))
                throw $this->createNotFoundException('The entity does not exist');

            $repository->remove($id);

            return new JsonResponse([]);
        }
        catch(\Exception $ex) {
            return new JsonResponse(['message' => $ex->getMessage()],  500);
        }

    }

    /***************** METHODES SECONDAIRES ****************/

    private function getEntityRepo(string $entityName) {
        try {
            $repo = $this->getDoctrine()->getRepository($entityName);
        }
        catch(\Exception $ex) {
            $repo = null;
        }

        return $repo;
    }


}
