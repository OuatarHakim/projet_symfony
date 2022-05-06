<?php

namespace App\Controller;

use App\Entity\MembrePersonnel;
use App\Form\MembrePersonnelType;
use App\Repository\MembrePersonnelRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MembrePersonnelController extends AbstractController
{
    const ROUTE_INDEX = "index_membrepersonnels";
    const ROUTE_SHOW = "show_membrepersonnel";
    const ROUTE_INSERT = "insert_membrepersonnel";
    const ROUTE_UPDATE = "update_membrepersonnel";
    const ROUTE_DELETE = "delete_membrepersonnel";

    /**
     * @Route("/membrepersonnel/{numPage<\d+>}", name=MembrePersonnelController::ROUTE_INDEX)
     * @param MembrePersonnelRepository $membre_personnelRepo
     * @param int $numPage
     * @return Response
     */
    public function index(MembrePersonnelRepository $membre_personnelRepo, int $numPage = 1): Response
    {
        $limit = 10;
        $offset = $limit * ($numPage - 1);
        try {
            $total = $membre_personnelRepo->getTotalMembrePersonnel();
        } catch (NoResultException $e) {
            return $this->renderError("Il n'existe pas d'entité membre personnel.", "index");
        } catch (NonUniqueResultException $e) {
            return $this->renderError($e->getMessage(), "index");
        }
        $offset = ($offset < 0 || $offset > $total) ? 0 : $offset;

        $membresPersonnels = $membre_personnelRepo->findBy([], ['nom' => 'ASC'], $limit, $offset);

        return $this->render('entity/membre_personnel/index.html.twig', [
            'routes' => [
                'index' => MembrePersonnelController::ROUTE_INDEX,
                'insert' => MembrePersonnelController::ROUTE_INSERT,
                'update' => MembrePersonnelController::ROUTE_UPDATE,
                'delete' => MembrePersonnelController::ROUTE_DELETE
            ],
            'title' => "Liste des membres personnels",
            'membres_personnels' => $membresPersonnels,
            'currentPage' => $numPage,
            'sum' => $total,
            'limit' => $limit
        ]);
    }

    /**
     * @Route("/membrepersonnel/{id}",name=MembrePersonnelController::ROUTE_SHOW)
     * @param int $id
     * @return Response
     */

    public function show(int $id): Response
    {
        $membre_personnel = $this->getDoctrine()
            ->getRepository(MembrePersonnel::class)
            ->find($id);

        if (!$membre_personnel) {
            throw $this->createNotFoundException('MembrePersonnel non trouve pour cet id_membre_personnel : ' . $id);
        }
        return $this->render('entity/membre_personnel/show.html.twig', [
            'title' => "Membre personnel $id",
            'membre_personnel' => $membre_personnel,
            'routeIndex' => MembrePersonnelController::ROUTE_INDEX
        ]);
    }

    /**
     * @Route("/membrepersonnel/insert", name=MembrePersonnelController::ROUTE_INSERT, priority="1")
     * @param Request $request
     * @return Response
     */
    public function insert(Request $request): Response
    {
        $title = "Créer un membre du personnel";
        $labelBtnSave = "Créer";
        $membre_personnel = new MembrePersonnel();
        return $this->handleForm($membre_personnel, $request, ['title' => $title, 'labelBtnSave' => $labelBtnSave]);
    }

    /**
     * @Route ("/membrepersonnel/update/{id}" , name=MembrePersonnelController::ROUTE_UPDATE)
     * @param int $id
     * @param Request $request
     * @param MembrePersonnelRepository $repo
     * @return Response
     */
    public function update(int $id, Request $request, MembrePersonnelRepository $repo): Response
    {
        $title = "Modifier un membre du personnel";
        $labelBtnSave = "Modifier";
        $membre_personnel = $repo->find($id);
        return $this->handleForm($membre_personnel, $request, ['title' => $title, 'labelBtnSave' => $labelBtnSave]);
    }

    /**
     * @Route ("/membrepersonnel/delete/{id}" ,name=MembrePersonnelController::ROUTE_DELETE)
     * @param int $id
     * @return RedirectResponse
     */
    public function delete(int $id): RedirectResponse
    {
        $membre_personnelManager = $this->getDoctrine()->getManager();
        $membre_personnel = $membre_personnelManager->getRepository(MembrePersonnel::class)->find($id);
        if (!$membre_personnel) {
            throw $this->createNotFoundException(
                'No Member found for id ' . $id
            );
        }
        $membre_personnelManager->remove($membre_personnel);
        $membre_personnelManager->flush();
        return $this->redirectToRoute(MembrePersonnelController::ROUTE_INDEX);

    }

    /**
     * @param MembrePersonnel $memper
     * @param Request $request
     * @param $params
     * @return Response
     */
    private function handleForm(MembrePersonnel $memper, Request $request, $params) : Response
    {
        $form = $this->createForm(MembrePersonnelType::class, $memper);
        $form->add("save", SubmitType::class, ['label' => $params['labelBtnSave']]);
        unset($params['labelBtnSave']);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($memper);
            $em->flush();

            return $this->redirectToRoute(MembrePersonnelController::ROUTE_SHOW, array('id' => $memper->getIdMemper()));
        }

        $params['memper'] = $memper;
        $params['form'] = $form->createView();
        $params['pathRetour'] = MembrePersonnelController::ROUTE_INDEX;

        return $this->render('entity/membre_personnel/form.html.twig', $params);
    }

    /**
     * @param string $errorMsg
     * @param string $routePrevious
     * @param array $params
     * @return Response
     */
    private function renderError(string $errorMsg, string $routePrevious, array $params = [])
    {
        return $this->render('entity/error.html.twig', [
            'error' => $errorMsg,
            'routePrevious' => $routePrevious,
            'paramsRoute' => $params
        ]);
    }
}
