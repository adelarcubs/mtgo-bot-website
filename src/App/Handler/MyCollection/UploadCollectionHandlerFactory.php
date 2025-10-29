<?php

declare(strict_types=1);

namespace App\Handler\MyCollection;

use App\Repository\MtgoItemRepository;
use App\Repository\UserCollectionItemRepository;
use App\Repository\UserRepository;
use App\Service\DekFileReader;
use Doctrine\ORM\EntityManagerInterface;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerInterface;

class UploadCollectionHandlerFactory
{
    public function __invoke(ContainerInterface $container): UploadCollectionHandler
    {
        $config = $container->get('config');

        return new UploadCollectionHandler(
            $container->get(UserCollectionItemRepository::class),
            $container->get(UserRepository::class),
            $container->get(TemplateRendererInterface::class),
            $config['upload_path'] ?? 'data/uploads',
            $container->get(EntityManagerInterface::class),
            $container->get(DekFileReader::class),
            $container->get(MtgoItemRepository::class)
        );
    }
}
