<?php

namespace App\Controller;

use App\Entity\Poule;
use App\Form\PouleType;

use App\Repository\PouleRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Exception;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PouleController extends AbstractController
{
    const ROUTE_INDEX = "index_poules";
    const ROUTE_SHOW = "show_poules";
    const ROUTE_INSERT = "insert_poules";
    const ROUTE_UPDATE = "update_poules";
    const ROUTE_DELETE = "delete_poules";

    /**
     * @Route("/poules/{numPage<\d+>}", name=PouleController::ROUTE_INDEX)
     * @param PouleRepository $pouleRepo
     * @param int $numPage
     * @return Response
     */
    public function index(PouleRepository $pouleRepo, int $numPage = 1): Response
    {
        $limit = 10;
        $offset = $limit * ($numPage-1);
        try {
            $total = $pouleRepo->getTotalPoule();
        } catch (NoResultException $e) {
            return $this->renderError("Il n'existe pas d'entité membre personnel.", "index");
        } catch (NonUniqueResultException $e) {
            return $this->renderError($e->getMessage(), "index");
        }
        $offset = ($offset < 0 || $offset > $total) ? 0:$offset;

        $poules = $pouleRepo->findBy([], ['id_poule'=>'ASC'], $limit, $offset);

        return $this->render('entity/poule/index.html.twig', [
            'routes' => [
                'index' => PouleController::ROUTE_INDEX,
                'insert' => PouleController::ROUTE_INSERT,
                'update' => PouleController::ROUTE_UPDATE,
                'delete' => PouleController::ROUTE_DELETE
            ],
            'title' => "Liste des poules",
            'poules' => $poules,
            'currentPage'=>$numPage,
            'sum'=>$total,
            'limit'=>$limit
        ]);
    }

    /**
     * @Route("/poule/{id}", name=PouleController::ROUTE_SHOW)
     * @param string $id
     * @return Response
     */

    public function show(string $id):Response
    {
        $poule = $this->getDoctrine()
            ->getRepository(Poule::class)
            ->find($id);

        if(!$poule){
            throw $this->createNotFoundException('Poule non trouvée pour cet identifiant : '.$id);
        }
        return $this->render('entity/poule/show.html.twig',[
            'title'=>"Poule $id",
            'poule'=>$poule,
            'routeIndex' => PouleController::ROUTE_INDEX
        ]);
    }

    /**
     * @Route("/poule/insert", name=PouleController::ROUTE_INSERT, priority="1")
     * @param Request $request
     * @return Response
     */
    public function insert(Request $request):Response
    {
        $poule = new Poule();
        $form = $this->createForm(PouleType::class, $poule);
        $form->add("save", SubmitType::class, ['label' => "Créer"]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $em = $this->getDoctrine()->getManager();
                $em->persist($poule);
                $em->flush();
            } catch (UniqueConstraintViolationException $e) {
                return $this->renderError("Une poule ayant la même lettre existe déjà", PouleController::ROUTE_INDEX);
            } catch (Exception $e) {
                return $this->renderError($e->getMessage(), PouleController::ROUTE_INDEX);
            }

            return $this->redirectToRoute(PouleController::ROUTE_SHOW, array('id' => $poule->getIdPoule()));
        }

        return $this->render('entity/poule/form.html.twig', array(
            'poule' => $poule,
            'title' => 'Créer une poule',
            'labelBtnSave' => "Créer",
            'form' => $form->createView(),
            'pathRetour' => PouleController::ROUTE_INDEX
        ));
    }

    /**
     * @Route ("/poule/update/{id}" , name=PouleController::ROUTE_UPDATE)
     * @param string $id
     * @param Request $request
     * @param PouleRepository $repo
     * @return Response
     */
    public function update(string $id, Request $request, PouleRepository $repo):Response
    {
        $poule = $repo->find($id);

        $form = $this->createForm(PouleType::class, $poule);
        $form->add("id_poule", TextType::class, [
            'disabled' => true,
            'attr' => ['readonly' => true]
        ]);
        $form->add("save", SubmitType::class, ['label' => "Modifier"]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($poule);
            $em->flush();

            return $this->redirectToRoute(PouleController::ROUTE_SHOW, array('id' => $poule->getIdPoule()));

        }

        return $this->render('entity/poule/form.html.twig', array(
            'labelBtnSave' => "Modifier",
            'title' => "Modifier une poule",
            'poule' => $poule,
            'form' => $form->createView(),
            'pathRetour' => PouleController::ROUTE_INDEX
        ));
    }

    /**
     * @Route ("/poule/delete/{id}", name=PouleController::ROUTE_DELETE)
     * @param string $id
     * @return RedirectResponse
     */
   public function delete(string $id):RedirectResponse
   {
       $pouleManager = $this->getDoctrine()->getManager();
       $poule = $pouleManager->getRepository(Poule::class)->find($id);

       if (!$poule) {
           throw $this->createNotFoundException(
               'Aucune poule trouve pour cet id : '.$id
           );
       }
       $pouleManager->remove($poule);
       $pouleManager->flush();
  return  $this->redirectToRoute(PouleController::ROUTE_INDEX);

   }

    /**
     * @param string $errorMsg
     * @param string $routePrevious
     * @param array $params
     * @return Response
     */
    private function renderError(string $errorMsg, string $routePrevious, array $params = []) : Response {
        return $this->render('entity/error.html.twig', [
            'error' => $errorMsg,
            'routePrevious' => $routePrevious,
            'paramsRoute' => $params
        ]);
    }
}
