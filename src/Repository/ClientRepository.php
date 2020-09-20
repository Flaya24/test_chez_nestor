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
        if(isset($data['birthDate'])) {
            $birthDate = date_create_from_format('d/m/Y', $data['birthDate']);
            if($birthDate)
                $client->setBirthDate($birthDate);
        }
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
        if(!$update) {
            if (!$email) {
                $errors['email'] = "L'email du client est obligatoire";
            } elseif ($this->sameEmailExists($email)) {
                $errors['email'] = "L'email du client est déjà utilisé";
            }
        }

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

    /**
     * Check if the same email already exists in database
     *
     * @param string $email
     * @return bool
     * @throws \Doctrine\DBAL\DBALException
     */
    public function sameEmailExists(string $email) {
        $conn = $this->getEntityManager()
            ->getConnection();

        $sql = "SELECT COUNT(id) FROM client WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(1, trim($email));
        $stmt->execute();

        // Vérification de l'existence d'un email similaire
        $numberEmail = intval($stmt->fetchColumn());
        return $numberEmail > 0;
    }


}
