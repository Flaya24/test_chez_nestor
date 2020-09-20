<?php

namespace App\Repository;

use App\Entity\Apartment;
use App\Entity\Room;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

class ApartmentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Apartment::class);
    }

    private function fromArray(Apartment $apartment, array $data) {
        // Set des valeurs
        if(isset($data['name']))
            $apartment->setName($data['name']);
        if(isset($data['street']))
            $apartment->setStreet($data['street']);
        if(isset($data['zipCode']))
            $apartment->setZipCode($data['zipCode']);
        if(isset($data['city']))
            $apartment->setCity($data['city']);
    }

    /**
     * Object creation process
     *
     * @param array $data
     * @return array
     */
    public function create(array $data) : array {
        $creationData = [];

        $creationData['apartment'] = new Apartment();
        $this->fromArray($creationData['apartment'], $data);

        // Creation de la chambre
        if(isset($data['room'])) {
            $roomRepo = $this->getEntityManager()->getRepository(Room::class);
            $creationData['room'] = $roomRepo->create($data['room']);
        }

        return $creationData;
    }

    /**
     * Object update process
     *
     * @param array $data
     * @return array
     */
    public function update(int $id, array $data) : array
    {
        $updateApartment = $this->findOneBy(['id' => $id]);
        $this->fromArray($updateApartment, $data);

        return [ 'apartment' => $updateApartment ];
    }

    /**
     * Object validation process
     *
     * @param array $creatioData : Contient l'appartment et la chambre (obligatoire en creation)
     * @param bool $update
     * @return array
     * @throws \Exception
     */
    public function validate(array $creatioData, bool $update = false) {
        $errors = [];
        $apartment = $creatioData['apartment'];

        if(!$apartment)
            throw new \Exception("Données Apartment invalides pour processus de validation");

        // Verification numéro
        $name = $apartment->getName();
        if(!$name && !$update)
            $errors['name'] = "Le nom de l'appartement est invalide";

        // Verification aire
        $street = $apartment->getStreet();
        if(!$street && !$update)
            $errors['street'] = "La rue de l'appartement est invalide";

        // Verification prix
        $zipCode = $apartment->getZipCode();
        if(!$zipCode && !$update)
            $errors['zipCode'] = "Le code postal de l'appartement est invalide";

        // Verification prix
        $city = $apartment->getCity();
        if(!$city && !$update)
            $errors['city'] = "La ville de l'appartement est invalide";

        // En creation seulement, validation des données pour la chambre
        if(!$update) {
            $room = isset($creatioData['room']) ? $creatioData['room'] : null;
            if(!$room) {
                $errors['room'] = "Vous devez renseigner les données d'au moins une chambre lors de la création d'un appartement";
            }
            else {
                $roomRepo = $this->getEntityManager()->getRepository(Room::class);
                $roomErrors = $roomRepo->validate($room);

                $errors = array_merge($roomErrors, $errors);
            }
        }

        return $errors;
    }

    /**
     * Object saving process
     *
     * @param $saveData : array or Apartment
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function save($saveData) {
        // Sauvegarde de l'appartment
        if(is_array($saveData) && isset($saveData['apartment']))
            $apartment = $saveData['apartment'];
        else
            $apartment = $saveData;

        $this->getEntityManager()->persist($apartment);

        // Sauvegarde de la première chambre (creation)
        if(is_array($saveData) && isset($saveData['room'])) {
            $saveData['room']->setApartmentId($apartment);
            $this->getEntityManager()->persist($saveData['room']);
        }

        $this->getEntityManager()->flush();

    }

    /**
     * Object removing process
     *
     * @param int $apartmentId
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(int $apartmentId) {
        $apartment = $this->findOneBy(['id' => $apartmentId]);

        $this->getEntityManager()->remove($apartment);
        $this->getEntityManager()->flush();
    }


}
