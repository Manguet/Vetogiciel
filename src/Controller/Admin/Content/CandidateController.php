<?php

namespace App\Controller\Admin\Content;

use App\Entity\Contents\Candidate;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 *
 * @Route("/admin/candidate", name="admin_candidate_")
 *
 * @Security("is_granted('ADMIN_CANDIDATE_ACCESS')")
 */
class CandidateController extends AbstractController
{
    /**
     * @Route("/show/{id}", name="show", methods={"GET", "POST"})
     * @Security("is_granted('ADMIN_CANDIDATE_SHOW', candidate)")
     *
     * @param Candidate $candidate
     *
     * @return Response
     */
    public function show(Candidate $candidate): Response
    {
        return $this->render('admin/content/candidate/show.html.twig', [
            'candidate' => $candidate
        ]);
    }

    /**
     * @Route("/validate/{id}", name="validate", methods={"GET", "POST"})
     * @Security("is_granted('ADMIN_CANDIDATE_EDIT', candidate)")
     *
     * @param Candidate $candidate
     * @param EntityManagerInterface $entityManager
     *
     * @return RedirectResponse
     */
    public function validate(Candidate $candidate, EntityManagerInterface $entityManager): RedirectResponse
    {
        $candidate->setIsResponseSend(true);

        $entityManager->flush();

        return $this->redirectToRoute('admin_joboffer_show', [
            'id' => $candidate->getJoboffer()->getId()
        ]);
    }

    /**
     * @Route("/delete/{id}", name="delete", methods={"POST"})
     * @Security("is_granted('ADMIN_CANDIDATE_DELETE', candidate)")
     *
     * @param Candidate $candidate
     * @param EntityManagerInterface $entityManager
     *
     * @return JsonResponse
     */
    public function delete(Candidate $candidate, EntityManagerInterface $entityManager): JsonResponse
    {
        if (!$candidate instanceof Candidate) {
            return new JsonResponse('Candidate Not Found', 404);
        }

        $entityManager->remove($candidate);
        $entityManager->flush();

        return new JsonResponse('Candidate deleted with success', 200);
    }
}