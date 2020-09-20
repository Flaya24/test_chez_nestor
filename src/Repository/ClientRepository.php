<?php

namespace App\Repository;

use App\Entity\Client;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

class ClientRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Client::class);
    }

    private function fromArray(Client $client, array $data) {
        // Set des valeurs
        if(isset($data['firstName']))
            $client->setFirstName($data['firstName']);
        if(isset($data['lastName']))
            $client->setLastName($data['lastName']);
        if(isset($data['email']))
            $client->setEmail($data['email']);
        if(isset($data['phone']))
            $client->setPhone($data['phone']);
        if(isset($data['birthDate']))
            $client->setBirthDate($data['birthDate']);
        if(isset($data['nationality']))
            $client->setNationality($data['nationality']);
    }

    /**
     * Object creation process
     *
     * @param array $data
     * @return Client
     */
    public function create(array $data) : Client {
        $newClient = new Client();
        $this->fromArray($newClient, $data);

        return $newClient;
    }

    /**
     * Object update process
     *
     * @param array $data
     * @return Client
     */
    public function update(int $id, array $data) : object {
        $updateClient = $this->findOneBy(['id' => $id]);
        $this->fromArray($updateClient, $data);

        return $updateClient;
    }

    /**
     * Object validation process
     *
     * @param Client $client
     * @param bool $update
     * @return array
     */
    public function validate(Client $client, bool $update = false) {
        $errors = [];

        // Verification email
        $email = $client->getEmail();
        if(!$email && !is_int($email)) {
            $errors['email'] = "L'email du client est obligatoire'";
        }
        // Contrainte d'unicité de l'email

        return $errors;
    }

    /**
     * Object saving process
     *
     * @param Client $client
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function save(Client $client) {
        $this->getEntityManager()->persist($client);
        $this->getEntityManager()->flush();
    }

    /**
     * Object removing process
     *
     * @param int $clientId
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(int $clientId) {
        $client = $this->findOneBy(['id' => $clientId]);

        $this->getEntityManager()->remove($client);
        $this->getEntityManager()->flush();
    }


}
