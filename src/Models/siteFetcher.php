<?php
/**
 * Created by PhpStorm.
 * User: sebastien
 * Date: 22-03-18
 * Time: 15:18
 */

namespace App\Models;


use App\Entity\Site;

use App\Entity\SiteBacklog;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\DomCrawler\Crawler;

class siteFetcher
{
    public function fetch(Site $site, ManagerRegistry $doctrine)
    {
        if (filter_var($site->getUrl(), FILTER_VALIDATE_URL)) {
            $parse = parse_url($site->getUrl());
            $site->setUrl($parse['scheme'] . '://' . $parse['host']);
            //dump($site->getUrl());
            /** @var $site Site */
            $existingSite = $doctrine->getRepository(Site::class)->findByUrl($site->getUrl());
            if (sizeof($existingSite)) {
                $site = $existingSite[0];
            }

            $curl = curl_init($site->getUrl());

            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_BINARYTRANSFER, true);
            $response = curl_exec($curl);
            $info = curl_getinfo($curl);
            //dump($info);
            curl_close($curl);


            if ($info['http_code'] == 200) {
                $crawler = new Crawler($response);

                //dump($response);

                if ($crawler->filter('meta')->count()) {
                    $descriptions = $crawler->filter('meta')->each(function (Crawler $node, $i) {
                        if ($node->attr('property') == "og:description") {
                            return $node->attr('content');
                        }
                        else
                        {
                            return null;
                        }
                    });

                    foreach ($descriptions as $description) {
                        if ($description != null) {
                            $site->setDescription($description);
                        }
                    }

                    $images = $crawler->filter('meta')->each(function (Crawler $node, $i) {
                        if ($node->attr('property') == "og:image") {
                            return $node->attr('content');
                        }
                        else
                        {
                            return null;
                        }
                    });

                    foreach ($images as $image) {
                        if ($image != null) {
                            $site->setThumbnailUrl($image);
                        }
                    }
                }


                if ($crawler->filter('title')->count()) {
                    $site->setTitle($crawler->filter('title')->first()->html());
                }

                $links = $this->getLinks($crawler);

                $links = array_unique($links);

                $em = $doctrine->getManager();
                $em->persist($site);
                foreach ($links as $link)
                {
                    if($link != null && $link != $site->getUrl())
                    {
                        $existingSite = $doctrine->getRepository(SiteBacklog::class)->findByUrl($links);
                        if(sizeof($existingSite) == 0){
                            $siteBacklog = new SiteBacklog();
                            $siteBacklog->setUrl($link);
                            $em->persist($siteBacklog);
                        }

                    }

                }
                $em->flush();

                return $site;

            }
        }


    }

    public function getLinks(Crawler $crawler)
    {
        $links = $crawler->filter('a')->each(function (Crawler $node, $i) {
            if (filter_var( $node->attr('href'), FILTER_VALIDATE_URL)) {
                $parse = parse_url($node->attr('href'));
                if(isset($parse['host']))
                {
                    $url = $parse['scheme'] . '://' . $parse['host'];
                    return $url;
                }
                else
                {
                    return null;
                }

            }
            else
            {
                return null;
            }
        });

        return $links;
    }
}