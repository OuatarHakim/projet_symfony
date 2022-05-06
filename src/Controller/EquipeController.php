<?php

namespace App\Controller;

use App\Entity\Equipe;

use App\Repository\EquipeRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\EquipeType;

class EquipeController extends AbstractController
{
    const ROUTE_INDEX = "index_equipes";
    const ROUTE_SHOW = "show_equipe";
    const ROUTE_INSERT = "insert_equipe";
    const ROUTE_UPDATE = "update_equipe";
    const ROUTE_DELETE = "delete_equipe";

    /**
     * @Route("/equipes/{numPage<\d+>}", name=EquipeController::ROUTE_INDEX);
     * @param EquipeRepository $equipeRepo
     * @param int $numPage
     * @return Response
     */
    public function index(EquipeRepository $equipeRepo, int $numPage = 1): Response
    {
        $limit = 10;
        $offset = $limit * ($numPage - 1);

        try {
            $total = $equipeRepo->getTotalEquipe();
        } catch (NoResultException $e) {
            return $this->renderError("Il n'existe pas d'entité équipe.", "index");
        } catch (NonUniqueResultException $e) {
            return $this->renderError($e->getMessage(), "index");
        }

        $offset = ($offset < 0 || $offset > $total) ? 0 : $offset;

        $equipes = $equipeRepo->findBy([], ['id_equipe' => 'ASC'], $limit, $offset);

        return $this->render('entity/equipe/index.html.twig', [
            'routes' => [
                'index' => EquipeController::ROUTE_INDEX,
                'insert' => EquipeController::ROUTE_INSERT,
                'update' => EquipeController::ROUTE_UPDATE,
                'delete' => EquipeController::ROUTE_DELETE
            ],
            'title' => "Liste des équipes",
            'equipes' => $equipes,
            'currentPage' => $numPage,
            'sum' => $total,
            'limit' => $limit
        ]);
    }


    /**
     * @Route("/equipe/{id}",name=EquipeController::ROUTE_SHOW)
     * @param string $id
     * @return Response
     */

    public function show(string $id): Response
    {
        $equipe = $this->getDoctrine()
            ->getRepository(Equipe::class)
            ->find($id);

        if (!$equipe) {
            throw $this->createNotFoundException("Equipe non trouvée pour l'identifiant : $id");
        }
        return $this->render('entity/equipe/show.html.twig', [
            'equipe' => $equipe,
            'title' => "Equipe $id",
            'routeIndex' => EquipeController::ROUTE_INDEX
        ]);
    }

    /**
     * @Route("/equipe/insert", name=EquipeController::ROUTE_INSERT, priority="1")
     * @param Request $request
     * @return Response
     */
    public function insert(Request $request): Response
    {
        $equipe = new Equipe();
        $form = $this->createForm(EquipeType::class, $equipe);
        $form->add("save", SubmitType::class, ['label' => "Créer"]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $em = $this->getDoctrine()->getManager();
                $em->persist($equipe);
                $em->flush();
            } catch (UniqueConstraintViolationException $e) {
                return $this->renderError("Une équipe ayant le même code CIO existe déjà",
                    EquipeController::ROUTE_INDEX);
            } catch (Exception $e) {
                return $this->renderError($e->getMessage(), EquipeController::ROUTE_INDEX);
            }

            return $this->redirectToRoute(EquipeController::ROUTE_SHOW, array('id' => $equipe->getIdEquipe()));
        }

        return $this->render('entity/equipe/form.html.twig', array(
            'title' => "Créer une équipe",
            'equipe' => $equipe,
            'form' => $form->createView(),
            'pathRetour' => EquipeController::ROUTE_INDEX
        ));
    }

    /**
     * @Route ("/equipe/update/{id}" , name=EquipeController::ROUTE_UPDATE)
     * @param string $id
     * @param Request $request
     * @return Response
     */
    public function update(string $id, Request $request): Response
    {
        $equipeManager = $this->getDoctrine()->getManager();
        $equipe = $equipeManager->getRepository(Equipe::class)->find($id);

        $form = $this->createForm(EquipeType::class, $equipe);

        //On remplace le champs de l'id équipe en champs readonly.
        $form->add("id_equipe", TextType::class, [
            'disabled' => true,
            'attr' => ['readonly' => true]
        ]);
        $form->add("save", SubmitType::class, ['label' => "Modifier"]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($equipe);
            $em->flush();

            return $this->redirectToRoute('show_equipe', array('id' => $equipe->getIdEquipe()));
        }
        return $this->render('entity/equipe/form.html.twig', array(
            'equipe' => $equipe,
            'title' => 'Modifier équipe',
            'form' => $form->createView(),
            'pathRetour' => EquipeController::ROUTE_INDEX
        ));

    }

    /**
     * @Route ("/equipe/delete/{id}" ,name=EquipeController::ROUTE_DELETE)
     * @param string $id
     * @return RedirectResponse
     */
    public function delete(string $id): RedirectResponse
    {
        $equipeManager = $this->getDoctrine()->getManager();
        $equipe = $equipeManager->getRepository(Equipe::class)->find($id);

        if (!$equipe) {
            throw $this->createNotFoundException(
                'No team found for id ' . $id
            );
        }
        $equipeManager->remove($equipe);
        $equipeManager->flush();
        return $this->redirectToRoute(EquipeController::ROUTE_INDEX);

    }

    /**
     * @param string $errorMsg
     * @param string $routePrevious
     * @param array $params
     * @return Response
     */
    private function renderError(string $errorMsg, string $routePrevious, array $params = []) : Response
    {
        return $this->render('entity/error.html.twig', [
            'error' => $errorMsg,
            'routePrevious' => $routePrevious,
            'paramsRoute' => $params
        ]);
    }
}
