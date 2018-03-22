<?php
/**
 * Created by PhpStorm.
 * User: sebastien
 * Date: 16-03-18
 * Time: 14:25
 */

namespace App\Controller;

use App\Entity\Site;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    public function Index(){

        $siteRepo = $this->getDoctrine()->getRepository(Site::class);
        $sites = $siteRepo->getRandom(3);

        return $this->render('index.html.twig', array(
            'sites' =>$sites
        ));
    }
}