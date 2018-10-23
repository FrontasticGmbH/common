<?php
require __DIR__ . '/../../vendor/autoload.php';

$builder = new \Symfony\Component\DependencyInjection\ContainerBuilder();
$extension = new \Frontastic\Common\ProductApiBundle\DependencyInjection\FrontasticCommonProductApiExtension();
$extension->load([], $builder);
$builder->compile();

return $builder->get('Frontastic\Common\ProductApiBundle\Domain\ProductApiFactory');
