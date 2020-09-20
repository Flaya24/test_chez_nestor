<?php

namespace App\Repository;

use App\Entity\Client;
use App\Entity\Reservation;
use App\Entity\Room;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

class ReservationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reservation::class);
    }

    private function fromArray(Reservation $reservation, array $data) {
        // Set des valeurs
        if(isset($data['clientId'])) {
            $clientRepo = $this->getEntityManager()->getRepository(Client::class);
            $client = $clientRepo->findOneBy([ 'id' => $data['clientId'] ]);
            if(is_object($client))
                $reservation->setClient($client);
        }
        if(isset($data['roomId'])) {
            $roomRepo = $this->getEntityManager()->getRepository(Room::class);
            $room = $roomRepo->findOneBy([ 'id' => $data['roomId'] ]);
            if(is_object($room))
                $reservation->setRoom($room);
        }
        if(isset($data['startDate'])) {
            $startDate = date_create_from_format('d/m/Y', $data['startDate']);
            if($startDate)
                $reservation->setStartDate($startDate);
        }
        if(isset($data['endDate'])) {
            $endDate = date_create_from_format('d/m/Y', $data['endDate']);
            if($endDate)
                $reservation->setEndDate($endDate);
        }

    }

    /**
     * Object creation process
     *
     * @param array $data
     * @return Reservation
     */
    public function create(array $data) : Reservation {
        $newReservation = new Reservation();
        $this->fromArray($newReservation, $data);

        return $newReservation;
    }

    /**
     * Object update process
     *
     * @param array $data
     * @return Reservation
     */
    public function update(int $id, array $data) : object {
        $updateReservation = $this->findOneBy(['id' => $id]);
        $this->fromArray($updateReservation, $data);

        return $updateReservation;
    }

    /**
     * Object validation process
     *
     * @param Reservation $reservation
     * @param bool $update
     * @return array
     */
    public function validate(Reservation $reservation, bool $update = false) {
        $errors = [];

        // Verification client
        $client = $reservation->getClient();
        if(!$client)
            $errors['clientId'] = "L'identifiant Client est invalide";

        // Verification chambre
        $room = $reservation->getRoom();
        if(!$room)
            $errors['roomId'] = "L'identifiant de la chambre est invalide'";

        // Verification date de début
        $startDate = $reservation->getStartDate();
        if(!$startDate)
            $errors['startDate'] = "La date de début de réservation est invalide";

        // Vérification conflit de réservation
        $clientHasReservations = $this->clientHasCurrentReservation($reservation->getClient()->getId(), $startDate);
        $roomHasReservations = $this->roomHasCurrentReservation($reservation->getRoom()->getId(), $startDate);
        if($clientHasReservations)
            $errors['startDate'] = "Le client a déjà une autre réservation pour la date indiquée";
        if($roomHasReservations)
            $errors['startDate'] = "La chambre a déjà une autre réservation pour la date indiquée";

        // Informations client manquantes
        $clientMissingInformations = $client->getMissingInformationForReservation();
        if(!empty($clientMissingInformations))
            $errors['clientId'] = "Vous devez fournir les informations suivantes sur le client avant d'effectuer la réservation : "
                . implode(", ", $clientMissingInformations);


        return $errors;
    }

    /**
     * Object saving process
     *
     * @param Reservation $reservation
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function save(Reservation $reservation) {
        $this->getEntityManager()->persist($reservation);
        $this->getEntityManager()->flush();
    }

    /**
     * Object removing process
     *
     * @param int $reservationId
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(int $reservationId) {
        $reservation = $this->findOneBy(['id' => $reservationId]);

        $this->getEntityManager()->remove($reservation);
        $this->getEntityManager()->flush();
    }

    /**
     * @param $entityColumn
     * @param $id
     * @param \DateTimeInterface|null $date
     * @return bool
     * @throws \Doctrine\DBAL\DBALException
     */
    public function entityHasCurrentReservation($entityColumn, $id, \DateTimeInterface $date = null) {
        $conn = $this->getEntityManager()->getConnection();

        // Si pas de date précisée, on prend la date actuelle
        if(!$date)
            $date = new \DateTime();

        // Verification de l'entité (client_id par défaut)
        if(!in_array($entityColumn, [ 'room_id', 'client_id ']))
            $entityColumn = 'client_id';

        // Requête
        $sql = "SELECT COUNT(id) FROM reservation
                WHERE " . $entityColumn  . " = ?
                AND ((end_date IS NULL AND DATE(?) >= start_date) 
                OR end_date IS NOT NULL AND DATE(?) BETWEEN start_date AND end_date)";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(1, $id);
        $stmt->bindValue(2, $date->format('Y-m-d'));
        $stmt->bindValue(3, $date->format('Y-m-d'));
        $stmt->execute();

        // Vérification de l'existence d'un email similaire
        $numberEntities = intval($stmt->fetchColumn());
        return $numberEntities > 0;
    }

    /**
     * Check if a client (by Id) has current reservation for a specific date (current date by default)
     *
     * @param $clientId
     * @param \DateTimeInterface|null $date
     * @return bool
     * @throws \Doctrine\DBAL\DBALException
     */
    public function clientHasCurrentReservation($clientId, \DateTimeInterface $date = null) : bool {
        return $this->entityHasCurrentReservation('client_id', $clientId, $date);
    }

    /**
     * Check if a room (by Id) has current reservation for a specific date (current date by default)
     *
     * @param $roomId
     * @param \DateTimeInterface|null $date
     * @return bool
     * @throws \Doctrine\DBAL\DBALException
     */
    public function roomHasCurrentReservation($roomId, \DateTimeInterface $date = null) : bool {
        return $this->entityHasCurrentReservation('room_id', $roomId, $date);
    }


}
