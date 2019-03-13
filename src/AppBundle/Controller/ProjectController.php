<?php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Project;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use AppBundle\Form\ProjectType;

/**
 * @Route("/projects")
 */
class ProjectController extends Controller{

  /**
   * @Route("/", name="indexProject")
   * @return \Symfony\Component\HttpFoundation\Response
   * @throws \LogicException
   */
   public function indexProject(Request $request){ // affichage de toutes les annonces
     $repository = $this->getDoctrine()->getRepository(Project::class);
     $projects = $repository->findAll();
     dump($projects);

     return $this->render('project/index.html.twig',[
       'projects'=>$projects
     ]);
   }

   /**
    * @Route("/delete/{id}", requirements={"id": "\d+"}, name="deleteProject")
    */
   public function deleteProject(Project $project, Request $request){ // suppression d'un projet
     $em = $this->getDoctrine()->getManager();
     $em->remove($project);
     $em->flush();

     return $this->redirectToRoute('indexProject');
   }

   /**
    * @Route("/add", name="newProject")
    * @return \Symfony\Component\HttpFoundation\Response
    * @throws \LogicException
    */
   public function newProject(Request $request){ // ajout d'un projet
     if($this->getUser() == null){ // possible que si un utilisateur est connecté
           return $this->redirectToRoute('fos_user_security_login');
       }

        // recupération de la liste de tous les utilisateurs
        $userManager = $this->get('fos_user.user_manager');
        $users = $userManager->findUsers();

       $project = new Project();
       $form = $this->createForm(ProjectType::class, $project, [
           'action' => $this->generateUrl('newProject')
       ]);
       $form->handleRequest($request);
       
       //$project->setUser($this->getUser());

       if(!isset($_POST['ajout'])){ // si le form n'a pas envoyé ajout en mode post
            if(!$form->isSubmitted() || !$form->isValid()){
                return $this->render('project/new.html.twig', [
                    'add_project_form' => $form->createView(),
                    'users' => $users,
                    'project' => $project,
                ]);
            }
       }
       else{ // sinon on effectue les requetes pour la modification cela veut dire que l'utilisateur à valider
          $user = $userManager->findUserBy(array('id'=>$request->request->get('project_user')));
          $project->setUser($user);
          $em = $this->getDoctrine()->getManager();
          $em->persist($project);
          $em->flush();
          unset($_POST);
          return $this->redirectToRoute('indexProject');
       }
   }

   /**
    * @Route("/edit/{id}", requirements={"id": "\d+"}, name="updateProject")
    */
    public function updateProject(Project $project, Request $request){ // modification d'un projet
      if($this->getUser() == null || $this->getUser() != $project->getUser() || $this->getUser() != $project->getUser() and !( in_array('ROLE_ADMIN', $this->getUser()->getRoles())) ){
          return $this->render('accesdenied.html.twig', [
              'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
          ]);
      }
      if(!isset($_POST['modif'])){ // si le form n'a pas envoyé ajout en mode post
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if(!$form->isSubmitted() || !$form->isValid()){
          return $this->render('project/edit.html.twig', [
            'project'=>$project,
            'edit_project_form'=>$form->createView(),
          ]);
        }
      }
      else{  // sinon on effectue les requetes pour la modification cela veut dire que l'utilisateur à valider
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();
        $em->flush();
        unset($_POST);
        return $this->redirectToRoute('indexProject');
      }
    }

    /**
     * @Route("/information/{id}", requirements={"id": "\d+"}, name="infoProject")
     */
    public function infoProject(Project $project, Request $request){ // affichage d'un projet
        return $this->render('project/info.html.twig', [
            'project'=>$project,
        ]);
    }
}
?>
