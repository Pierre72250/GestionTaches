<?php
/**
 * Created by PhpStorm.
 * User: Pierre
 * Date: 27/03/2018
 * Time: 04:04
 */

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\User;

/**
 * @Route("/users")
 */
class UserController extends Controller
{
    /**
     * @Route("/", name="indexUsers")
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \LogicException
     */
    public function indexLocation(Request $request){
        if($this->getUser() == null || !( in_array('ROLE_ADMIN', $this->getUser()->getRoles())) ){
            return $this->render('accesdenied.html.twig', [
                'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
            ]);
        }
        $repository = $this->getDoctrine()->getRepository(User::class);
        $users = $repository->findAll();

        return $this->render('user/index.html.twig',[
            'users'=>$users
        ]);
    }

    /**
     * @Route("/desable/{id}", requirements={"id": "\d+"}, name="desableUser")
     */
    public function desableUser(User $user, Request $request){
        $user->setEnabled(false);
        $em = $this->getDoctrine()->getManager();
        $em->flush();
        return $this->redirectToRoute('indexUsers');
    }

    /**
     * @Route("/enable/{id}", requirements={"id": "\d+"}, name="enableUser")
     */
    public function enableUser(User $user, Request $request){
        $user->setEnabled(true);
        $em = $this->getDoctrine()->getManager();
        $em->flush();
        return $this->redirectToRoute('indexUsers');
    }

    /**
     * @Route("/profil/{id}", requirements={"id": "\d+"}, name="infoProfil")
     */
    public function infoLocation(User $user, Request $request){ // page sur l'info de l'utilisateur

        return $this->render('user/show.html.twig', [
            'user'=>$user,
        ]);
    }
}
