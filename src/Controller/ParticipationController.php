<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\participationEv;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ParticipationController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

//    public function participerEvenement(int $id_ev, int $idUser)
//    {
//        $participationEv = new participationEv();
//        $participationEv->setIdEv($id_ev);
//        $participationEv->setIdUser($idUser);
//
//        $this->entityManager->persist($participationEvenement);
//        $this->entityManager->flush();
//    }

}
