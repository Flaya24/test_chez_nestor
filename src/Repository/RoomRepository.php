<?php

namespace App\Repository;

use App\Entity\Apartment;
use App\Entity\Room;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;


class RoomRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Room::class);
    }

    private function fromArray(Room $room, $data) {
        // Set des valeurs
        if(isset($data['number']))
            $room->setNumber(intval($data['number']));
        if(isset($data['area']))
            $room->setArea(floatval($data['area']));
        if(isset($data['price']))
            $room->setPrice(intval($data['price']));
        if(isset($data['apartment_id'])) {
            $apartmentRepo = $this->getEntityManager()->getRepository(Apartment::class);
            $apartment = $apartmentRepo->findOneBy([ 'id' => $data['apartment_id'] ]);
            if(is_object($apartment))
                $room->setApartmentId($apartment);
        }
    }

    /**
     * Object creation process
     *
     * @param $data
     * @return Room
     */
    public function create($data) : Room {
        $newRoom = new Room();
        $this->fromArray($newRoom, $data);

        return $newRoom;
    }

    /**
     * Object update process
     *
     * @param array $data
     * @return Room
     */
    public function update(int $id, array $data) : object {
        $updateRoom = $this->findOneBy(['id' => $id]);
        $this->fromArray($updateRoom, $data);

        return $updateRoom;
    }

    /**
     * Object validation process
     *
     * @param Room $room
     * @param bool $update
     * @return array
     */
    public function validate(Room $room, bool $update = false) {
        $errors = [];

        // Verification numéro
        $number = $room->getNumber();
        if(!$number && !is_int($number))
            $errors['number'] = "Le numéro de la chambre est invalide";

        // Verification aire
        $area = $room->getArea();
        if(!$area && !is_float($area))
            $errors['area'] = "L'aire de la chambre est invalide";

        // Verification prix
        $price = $room->getPrice();
        if(!$price && !is_int($price))
            $errors['price'] = "Le prix de la chambre est invalide";

        return $errors;
    }

    /**
     * Object saving process
     *
     * @param Room $room
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function save(Room $room) {
        // Verification contrainte Appartment
        $apartment = $room->getApartmentId();
        if(!$apartment && !is_object($apartment))
            throw new \Exception("L'identifiant Appartement lié à la chambre est invalide");

        $this->getEntityManager()->persist($room);
        $this->getEntityManager()->flush();
    }

    /**
     * Object removing process
     *
     * @param Room $room
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(int $roomId) {
        $room = $this->findOneBy(['id' => $roomId]);

        // Vérification de l'appartement (doit toujours posséder au moins une chambre)
        $apartmentRepo = $this->getEntityManager()->getRepository(Apartment::class);
        $apartment = $apartmentRepo->findOneBy([ 'id' => $room->getApartmentId() ]);
        if($apartment->getRooms()->count() <= 1)
            throw new \Exception("La chambre ne peut être supprimée car l'appartement doit posséder au moins une chambre");

        $this->getEntityManager()->remove($room);
        $this->getEntityManager()->flush();
    }



}
