<?php
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;
use App\Controller\NewsController;

$collection = new RouteCollection();
$collection->add('news_list', new Route('/news', array(
    '_controller' => [NewsController::class, 'list']
)));
$collection->add('news_show', new Route('/news/{slug}', array(
    '_controller' => [NewsController::class, 'show']
)));

return $collection;