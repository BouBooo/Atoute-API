<?php

namespace App\Controller\Admin;

use App\Repository\FailedJobRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/admin/jobs", name="admin_failed_jobs")
 */
class FailedJobController extends AbstractController
{
    /**
     * @Route("/", name="")
     */
    public function index(FailedJobRepository $failedJobRepository): Response
    {
        return $this->render('jobs/index.html.twig', [
            'jobs' => $failedJobRepository->findAll(),
        ]);
    }

    /**
     * @Route("/remove/{id}", name="_remove", methods={"DELETE"})
     */
    public function remove(int $id, FailedJobRepository $failedJobRepository): RedirectResponse
    {
        $failedJobRepository->reject($id);
        return $this->redirectToRoute('admin_failed_jobs');
    }

    /**
     * @Route("/retry/{id}", name="_retry", methods={"POST"})
     */
    public function retry(
        int $id,
        FailedJobRepository $failedJobRepository,
        MessageBusInterface $busInterface
    ): RedirectResponse
    {
        $message = $failedJobRepository->find($id)->getMessage();
        $busInterface->dispatch($message);
        $failedJobRepository->reject($id);
        return $this->redirectToRoute('admin_failed_jobs');
    }
}
