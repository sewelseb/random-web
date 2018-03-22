<?php
/**
 * Created by PhpStorm.
 * User: sebastien
 * Date: 22-03-18
 * Time: 15:03
 */

namespace App\Command;


use App\Entity\Site;
use App\Entity\SiteBacklog;
use App\Models\siteFetcher;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CrawlerCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('app:crawler')

            // the short description shown while running "php bin/console list"
            ->setDescription('launch the crawler.')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command will crawl websites');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            'Crawling websites',
            '==================',
            '',
        ]);

        $siteBacklogRepo = $this->getContainer()->get('doctrine')->getRepository(SiteBacklog::class);
        $backlogSites = $siteBacklogRepo->getUnvisitedWebSites(10);

        $em = $this->getContainer()->get('doctrine')->getManager();
        foreach ($backlogSites as $backlogSite)
        {
            /** @var $backlogSite \App\Entity\SiteBacklog */
            $site = new Site();
            $site->setUrl($backlogSite->getUrl());
            $fetcher = new siteFetcher();
            $fetcher->fetch($site, $this->getContainer()->get('doctrine'));
            $backlogSite->setVisited(true);
            $em->persist($backlogSite);
        }
        $em->flush();
    }

}