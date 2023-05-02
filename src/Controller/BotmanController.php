<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use BotMan\BotMan\BotMan;
class BotmanController extends AbstractController
{
    #[Route('/botman', name: 'app_botman')]
    public function handle(Request $request): Response
    {
        $botman = $this->get('BotMan\BotMan\BotMan');
        $botman->listen();

        return new Response();
    }

    public function handleMessage(BotMan $bot)
    {
        $bot->reply('Hello! How can I help you?');
    }
}
