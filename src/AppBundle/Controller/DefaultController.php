<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
        ]);
    }

    /**
     * Normally this would go to a separate controller or even a bundle of it's own
     * but for the sake of simplicity and time saving making it here
     *
     * @Route("/api/jobs/list", name="jobs")
     * @Method("GET")
     */
    public function jobsListAction(Request $request)
    {
        // check if jobs_storage parameter is defined
        $errors = array();
        if ($this->container->hasParameter('jobs_storage')) {
            $file_path = $this->container->getParameter('jobs_storage');

            $file_system = new Filesystem();
            if ($file_system->exists($file_path . '/jobs.json')) {
                $data = file_get_contents($file_path . '/jobs.json');
                $response = new Response();
                $response->setContent($data);
                $response->headers->set('Content-Type', 'application/json');
                return $response;
            } else {
                $errors[] = [400 => 'jobs.json not found'];
            }

        } else {
            $errors[] = [500 => 'Missing jobs_storage in parameters.yml!'];
        }
        return new JsonResponse((object) ["errors" => $errors]);
    }
}
