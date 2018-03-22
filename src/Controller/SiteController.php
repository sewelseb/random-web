<?php

namespace App\Controller;

use App\Entity\Site;
use App\Form\SiteType;
use App\Models\siteFetcher;
use http\Env\Request;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class SiteController extends Controller
{
    /**
     * @Route("/site", name="site")
     */
    public function index()
    {
        return $this->render('site/index.html.twig', [
            'controller_name' => 'SiteController',
        ]);
    }

    public function create(\Symfony\Component\HttpFoundation\Request $request)
    {
        $site = new Site();
        $form = $this->createForm(SiteType::class, $site);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $site = $form->getData();
            //dump(filter_var($site->getUrl(), FILTER_VALIDATE_URL));
            $fetcher = new siteFetcher();
            $site = $fetcher->fetch($site, $this->getDoctrine());

            return $this->render('site/createForm.html.twig', [
                'form' => $form->createView(),
                'site' => $site
            ]);

        }


        return $this->render('site/createForm.html.twig',[
            'form' => $form->createView()
        ]);
    }
}
