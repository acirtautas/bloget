<?php

namespace Bloget;

use Goutte\Client;
use Symfony\Component\DomCrawler\Crawler;

class Grabber
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->setClient($client);
    }

    /**
     * @param Client $client
     */
    public function setClient(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @return Client
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @param string $lastPostUrl
     * @param string $olderLink
     * @param string $targetDir
     */
    public function grab($lastPostUrl, $olderLink, $targetDir)
    {
        $client  = $this->getClient();
        $crawler = $client->request('GET', $lastPostUrl);
        $counter = 0;

        do {
            $counter ++;

            $data = $this->extractData($crawler);

            $this->saveDataToFile($counter, $data, $targetDir);

            $olderPostLink = $crawler->selectLink($olderLink)->link();
            $crawler = $client->click($olderPostLink);
        } while ($crawler->selectLink($olderLink)->count());
    }

    /**
     * @param Crawler $crawler
     *
     * @return array
     */
    public function extractData(Crawler $crawler)
    {
        global $title, $content;

        $title = $content = '';

        $crawler->filter('#Blog1 h3.post-title')->each(
            function ($node) {
                global $title;
                $title = trim($node->text());
            }
        );
        $crawler->filter('#Blog1 div.post-body')->each(
            function ($node) {
                global $content;
                $content = trim($node->text());
            }
        );

        return array('title' =>$title, 'content' => $content);
    }

    /**
     * @param $counter
     * @param $data
     * @param $target
     * @param string $extension
     */
    public function saveDataToFile($counter, $data, $target, $extension = 'txt')
    {
        echo $counter, ' - ',$data['title'], PHP_EOL;

        file_put_contents("{$target}/{$counter}.{$extension}", $data['title']."\n\n".$data['content']);
    }
}
