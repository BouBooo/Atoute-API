<?php

namespace App\Controller\Admin;

use App\Repository\FailedJobRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/admin", name="admin_")
 */
class FailedJobController extends AbstractController
{
    /**
     * @Route("/jobs", name="failed_job")
     */
    public function index(FailedJobRepository $failedJobRepository): Response
    {
        return $this->render('jobs/index.html.twig', [
            'jobs' => $failedJobRepository->findAll(),
        ]);
    }
}
