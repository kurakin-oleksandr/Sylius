<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ResourceBundle\Routing;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ResourceBundle\Routing\ResourceLoader;
use Sylius\Bundle\ResourceBundle\Routing\RouteFactoryInterface;
use Sylius\Component\Resource\Metadata\MetadataInterface;
use Sylius\Component\Resource\Metadata\RegistryInterface;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class ResourceLoaderSpec extends ObjectBehavior
{
    function let(RegistryInterface $resourceRegistry, RouteFactoryInterface $routeFactory)
    {
        $this->beConstructedWith($resourceRegistry, $routeFactory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ResourceLoader::class);
    }

    function it_is_a_Symfony_routing_loader()
    {
        $this->shouldImplement(LoaderInterface::class);
    }

    function it_processes_configuration_and_throws_exception_if_invalid()
    {
        $configuration =
<<<EOT
foo: bar
only: string
EOT;

        $this
            ->shouldThrow(InvalidConfigurationException::class)
            ->during('load', [$configuration, 'sylius.resource']);
    }

    function it_throws_an_exception_if_invalid_resource_configured(RegistryInterface $resourceRegistry)
    {
        $resourceRegistry->get('sylius.foo')->willThrow(new \InvalidArgumentException());

        $configuration =
<<<EOT
alias: sylius.foo
EOT;

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('load', [$configuration, 'sylius.resource']);
    }

    function it_generates_routing_based_on_resource_configuration(
        RegistryInterface $resourceRegistry,
        MetadataInterface $metadata,
        RouteFactoryInterface $routeFactory,
        RouteCollection $routeCollection,
        Route $showRoute,
        Route $indexRoute,
        Route $createRoute,
        Route $updateRoute,
        Route $deleteRoute
    ) {
        $resourceRegistry->get('sylius.product')->willReturn($metadata);
        $metadata->getApplicationName()->willReturn('sylius');
        $metadata->getName()->willReturn('product');
        $metadata->getPluralName()->willReturn('products');
        $metadata->getServiceId('controller')->willReturn('sylius.controller.product');

        $routeFactory->createRouteCollection()->willReturn($routeCollection);

        $configuration =
<<<EOT
alias: sylius.product
EOT;

        $showDefaults = [
            '_controller' => 'sylius.controller.product:showAction',
            '_sylius'     => [
                'permission' => false,
            ],
        ];
        $routeFactory->createRoute('/products/{id}', $showDefaults, [], [], '', [], ['GET'])->willReturn($showRoute);
        $routeCollection->add('sylius_product_show', $showRoute)->shouldBeCalled();

        $indexDefaults = [
            '_controller' => 'sylius.controller.product:indexAction',
            '_sylius'     => [
                'permission' => false,
            ],
        ];
        $routeFactory->createRoute('/products/', $indexDefaults, [], [], '', [], ['GET'])->willReturn($indexRoute);
        $routeCollection->add('sylius_product_index', $indexRoute)->shouldBeCalled();

        $createDefaults = [
            '_controller' => 'sylius.controller.product:createAction',
            '_sylius'     => [
                'permission' => false,
            ],
        ];
        $routeFactory->createRoute('/products/new', $createDefaults, [], [], '', [], ['GET', 'POST'])->willReturn($createRoute);
        $routeCollection->add('sylius_product_create', $createRoute)->shouldBeCalled();

        $updateDefaults = [
            '_controller' => 'sylius.controller.product:updateAction',
            '_sylius'     => [
                'permission' => false,
            ],
        ];
        $routeFactory->createRoute('/products/{id}/edit', $updateDefaults, [], [], '', [], ['GET', 'PUT', 'PATCH'])->willReturn($updateRoute);
        $routeCollection->add('sylius_product_update', $updateRoute)->shouldBeCalled();

        $deleteDefaults = [
            '_controller' => 'sylius.controller.product:deleteAction',
            '_sylius'     => [
                'permission' => false,
            ],
        ];
        $routeFactory->createRoute('/products/{id}', $deleteDefaults, [], [], '', [], ['DELETE'])->willReturn($deleteRoute);
        $routeCollection->add('sylius_product_delete', $deleteRoute)->shouldBeCalled();

        $this->load($configuration, 'sylius.resource')->shouldReturn($routeCollection);
    }

    function it_generates_urlized_paths_for_resources_with_multiple_words_in_name(
        RegistryInterface $resourceRegistry,
        MetadataInterface $metadata,
        RouteFactoryInterface $routeFactory,
        RouteCollection $routeCollection,
        Route $showRoute,
        Route $indexRoute,
        Route $createRoute,
        Route $updateRoute,
        Route $deleteRoute
    ) {
        $resourceRegistry->get('sylius.product_option')->willReturn($metadata);
        $metadata->getApplicationName()->willReturn('sylius');
        $metadata->getName()->willReturn('product_option');
        $metadata->getPluralName()->willReturn('product_options');
        $metadata->getServiceId('controller')->willReturn('sylius.controller.product_option');

        $routeFactory->createRouteCollection()->willReturn($routeCollection);

        $configuration =
<<<EOT
alias: sylius.product_option
EOT;

        $showDefaults = [
            '_controller' => 'sylius.controller.product_option:showAction',
            '_sylius'     => [
                'permission' => false,
            ],
        ];
        $routeFactory->createRoute('/product-options/{id}', $showDefaults, [], [], '', [], ['GET'])->willReturn($showRoute);
        $routeCollection->add('sylius_product_option_show', $showRoute)->shouldBeCalled();

        $indexDefaults = [
            '_controller' => 'sylius.controller.product_option:indexAction',
            '_sylius'     => [
                'permission' => false,
            ],
        ];
        $routeFactory->createRoute('/product-options/', $indexDefaults, [], [], '', [], ['GET'])->willReturn($indexRoute);
        $routeCollection->add('sylius_product_option_index', $indexRoute)->shouldBeCalled();

        $createDefaults = [
            '_controller' => 'sylius.controller.product_option:createAction',
            '_sylius'     => [
                'permission' => false,
            ],
        ];
        $routeFactory->createRoute('/product-options/new', $createDefaults, [], [], '', [], ['GET', 'POST'])->willReturn($createRoute);
        $routeCollection->add('sylius_product_option_create', $createRoute)->shouldBeCalled();

        $updateDefaults = [
            '_controller' => 'sylius.controller.product_option:updateAction',
            '_sylius'     => [
                'permission' => false,
            ],
        ];
        $routeFactory->createRoute('/product-options/{id}/edit', $updateDefaults, [], [], '', [], ['GET', 'PUT', 'PATCH'])->willReturn($updateRoute);
        $routeCollection->add('sylius_product_option_update', $updateRoute)->shouldBeCalled();

        $deleteDefaults = [
            '_controller' => 'sylius.controller.product_option:deleteAction',
            '_sylius'     => [
                'permission' => false,
            ],
        ];
        $routeFactory->createRoute('/product-options/{id}', $deleteDefaults, [], [], '', [], ['DELETE'])->willReturn($deleteRoute);
        $routeCollection->add('sylius_product_option_delete', $deleteRoute)->shouldBeCalled();

        $this->load($configuration, 'sylius.resource')->shouldReturn($routeCollection);
    }

    function it_generates_urlized_paths_for_resources_with_custom_identifier(
        RegistryInterface $resourceRegistry,
        MetadataInterface $metadata,
        RouteFactoryInterface $routeFactory,
        RouteCollection $routeCollection,
        Route $showRoute,
        Route $indexRoute,
        Route $createRoute,
        Route $updateRoute,
        Route $deleteRoute
    ) {
        $resourceRegistry->get('sylius.product_option')->willReturn($metadata);
        $metadata->getApplicationName()->willReturn('sylius');
        $metadata->getName()->willReturn('product_option');
        $metadata->getPluralName()->willReturn('product_options');
        $metadata->getServiceId('controller')->willReturn('sylius.controller.product_option');

        $routeFactory->createRouteCollection()->willReturn($routeCollection);

        $configuration =
<<<EOT
alias: sylius.product_option
identifier: code
criteria: 
    code: \$code
EOT;

        $showDefaults = [
            '_controller' => 'sylius.controller.product_option:showAction',
            '_sylius'     => [
                'permission' => false,
                'criteria' => [
                    'code' => '$code',
                ],
            ],
        ];
        $routeFactory->createRoute('/product-options/{code}', $showDefaults, [], [], '', [], ['GET'])->willReturn($showRoute);
        $routeCollection->add('sylius_product_option_show', $showRoute)->shouldBeCalled();

        $indexDefaults = [
            '_controller' => 'sylius.controller.product_option:indexAction',
            '_sylius'     => [
                'permission' => false,
            ],
        ];
        $routeFactory->createRoute('/product-options/', $indexDefaults, [], [], '', [], ['GET'])->willReturn($indexRoute);
        $routeCollection->add('sylius_product_option_index', $indexRoute)->shouldBeCalled();

        $createDefaults = [
            '_controller' => 'sylius.controller.product_option:createAction',
            '_sylius'     => [
                'permission' => false,
            ],
        ];
        $routeFactory->createRoute('/product-options/new', $createDefaults, [], [], '', [], ['GET', 'POST'])->willReturn($createRoute);
        $routeCollection->add('sylius_product_option_create', $createRoute)->shouldBeCalled();

        $updateDefaults = [
            '_controller' => 'sylius.controller.product_option:updateAction',
            '_sylius'     => [
                'permission' => false,
                'criteria' => [
                    'code' => '$code',
                ],
            ],
        ];
        $routeFactory->createRoute('/product-options/{code}/edit', $updateDefaults, [], [], '', [], ['GET', 'PUT', 'PATCH'])->willReturn($updateRoute);
        $routeCollection->add('sylius_product_option_update', $updateRoute)->shouldBeCalled();

        $deleteDefaults = [
            '_controller' => 'sylius.controller.product_option:deleteAction',
            '_sylius'     => [
                'permission' => false,
                'criteria' => [
                    'code' => '$code',
                ],
            ],
        ];
        $routeFactory->createRoute('/product-options/{code}', $deleteDefaults, [], [], '', [], ['DELETE'])->willReturn($deleteRoute);
        $routeCollection->add('sylius_product_option_delete', $deleteRoute)->shouldBeCalled();

        $this->load($configuration, 'sylius.resource')->shouldReturn($routeCollection);
    }

    function it_generates_routing_with_custom_path_if_specified(
        RegistryInterface $resourceRegistry,
        MetadataInterface $metadata,
        RouteFactoryInterface $routeFactory,
        RouteCollection $routeCollection,
        Route $showRoute,
        Route $indexRoute,
        Route $createRoute,
        Route $updateRoute,
        Route $deleteRoute
    ) {
        $resourceRegistry->get('sylius.product')->willReturn($metadata);
        $metadata->getApplicationName()->willReturn('sylius');
        $metadata->getName()->willReturn('product');
        $metadata->getPluralName()->willReturn('products');
        $metadata->getServiceId('controller')->willReturn('sylius.controller.product');

        $routeFactory->createRouteCollection()->willReturn($routeCollection);

        $configuration =
<<<EOT
alias: sylius.product
path: super-duper-products
EOT;

        $showDefaults = [
            '_controller' => 'sylius.controller.product:showAction',
            '_sylius'     => [
                'permission' => false,
            ],
        ];
        $routeFactory->createRoute('/super-duper-products/{id}', $showDefaults, [], [], '', [], ['GET'])->willReturn($showRoute);
        $routeCollection->add('sylius_product_show', $showRoute)->shouldBeCalled();

        $indexDefaults = [
            '_controller' => 'sylius.controller.product:indexAction',
            '_sylius'     => [
                'permission' => false,
            ],
        ];
        $routeFactory->createRoute('/super-duper-products/', $indexDefaults, [], [], '', [], ['GET'])->willReturn($indexRoute);
        $routeCollection->add('sylius_product_index', $indexRoute)->shouldBeCalled();

        $createDefaults = [
            '_controller' => 'sylius.controller.product:createAction',
            '_sylius'     => [
                'permission' => false,
            ],
        ];
        $routeFactory->createRoute('/super-duper-products/new', $createDefaults, [], [], '', [], ['GET', 'POST'])->willReturn($createRoute);
        $routeCollection->add('sylius_product_create', $createRoute)->shouldBeCalled();

        $updateDefaults = [
            '_controller' => 'sylius.controller.product:updateAction',
            '_sylius'     => [
                'permission' => false,
            ],
        ];
        $routeFactory->createRoute('/super-duper-products/{id}/edit', $updateDefaults, [], [], '', [], ['GET', 'PUT', 'PATCH'])->willReturn($updateRoute);
        $routeCollection->add('sylius_product_update', $updateRoute)->shouldBeCalled();

        $deleteDefaults = [
            '_controller' => 'sylius.controller.product:deleteAction',
            '_sylius'     => [
                'permission' => false,
            ],
        ];
        $routeFactory->createRoute('/super-duper-products/{id}', $deleteDefaults, [], [], '', [], ['DELETE'])->willReturn($deleteRoute);
        $routeCollection->add('sylius_product_delete', $deleteRoute)->shouldBeCalled();

        $this->load($configuration, 'sylius.resource')->shouldReturn($routeCollection);
    }

    function it_generates_routing_with_custom_form_if_specified(
        RegistryInterface $resourceRegistry,
        MetadataInterface $metadata,
        RouteFactoryInterface $routeFactory,
        RouteCollection $routeCollection,
        Route $showRoute,
        Route $indexRoute,
        Route $createRoute,
        Route $updateRoute,
        Route $deleteRoute
    ) {
        $resourceRegistry->get('sylius.product')->willReturn($metadata);
        $metadata->getApplicationName()->willReturn('sylius');
        $metadata->getName()->willReturn('product');
        $metadata->getPluralName()->willReturn('products');
        $metadata->getServiceId('controller')->willReturn('sylius.controller.product');

        $routeFactory->createRouteCollection()->willReturn($routeCollection);

        $configuration =
<<<EOT
alias: sylius.product
form: sylius_product_custom
EOT;

        $showDefaults = [
            '_controller' => 'sylius.controller.product:showAction',
            '_sylius'     => [
                'permission' => false,
            ],
        ];
        $routeFactory->createRoute('/products/{id}', $showDefaults, [], [], '', [], ['GET'])->willReturn($showRoute);
        $routeCollection->add('sylius_product_show', $showRoute)->shouldBeCalled();

        $indexDefaults = [
            '_controller' => 'sylius.controller.product:indexAction',
            '_sylius'     => [
                'permission' => false,
            ],
        ];
        $routeFactory->createRoute('/products/', $indexDefaults, [], [], '', [], ['GET'])->willReturn($indexRoute);
        $routeCollection->add('sylius_product_index', $indexRoute)->shouldBeCalled();

        $createDefaults = [
            '_controller' => 'sylius.controller.product:createAction',
            '_sylius' => [
                'form'       => 'sylius_product_custom',
                'permission' => false,
            ],
        ];
        $routeFactory->createRoute('/products/new', $createDefaults, [], [], '', [], ['GET', 'POST'])->willReturn($createRoute);
        $routeCollection->add('sylius_product_create', $createRoute)->shouldBeCalled();

        $updateDefaults = [
            '_controller' => 'sylius.controller.product:updateAction',
            '_sylius' => [
                'form'       => 'sylius_product_custom',
                'permission' => false,
            ],
        ];
        $routeFactory->createRoute('/products/{id}/edit', $updateDefaults, [], [], '', [], ['GET', 'PUT', 'PATCH'])->willReturn($updateRoute);
        $routeCollection->add('sylius_product_update', $updateRoute)->shouldBeCalled();

        $deleteDefaults = [
            '_controller' => 'sylius.controller.product:deleteAction',
            '_sylius' => [
                'permission' => false,
            ],
        ];
        $routeFactory->createRoute('/products/{id}', $deleteDefaults, [], [], '', [], ['DELETE'])->willReturn($deleteRoute);
        $routeCollection->add('sylius_product_delete', $deleteRoute)->shouldBeCalled();

        $this->load($configuration, 'sylius.resource')->shouldReturn($routeCollection);
    }

    function it_generates_routing_for_a_section(
        RegistryInterface $resourceRegistry,
        MetadataInterface $metadata,
        RouteFactoryInterface $routeFactory,
        RouteCollection $routeCollection,
        Route $showRoute,
        Route $indexRoute,
        Route $createRoute,
        Route $updateRoute,
        Route $deleteRoute
    ) {
        $resourceRegistry->get('sylius.product')->willReturn($metadata);
        $metadata->getApplicationName()->willReturn('sylius');
        $metadata->getName()->willReturn('product');
        $metadata->getPluralName()->willReturn('products');
        $metadata->getServiceId('controller')->willReturn('sylius.controller.product');

        $routeFactory->createRouteCollection()->willReturn($routeCollection);

        $configuration =
<<<EOT
alias: sylius.product
section: admin
EOT;

        $showDefaults = [
            '_controller' => 'sylius.controller.product:showAction',
            '_sylius' => [
                'section'    => 'admin',
                'permission' => false,
            ],
        ];
        $routeFactory->createRoute('/products/{id}', $showDefaults, [], [], '', [], ['GET'])->willReturn($showRoute);
        $routeCollection->add('sylius_admin_product_show', $showRoute)->shouldBeCalled();

        $indexDefaults = [
            '_controller' => 'sylius.controller.product:indexAction',
            '_sylius' => [
                'section'    => 'admin',
                'permission' => false,
            ],
        ];
        $routeFactory->createRoute('/products/', $indexDefaults, [], [], '', [], ['GET'])->willReturn($indexRoute);
        $routeCollection->add('sylius_admin_product_index', $indexRoute)->shouldBeCalled();

        $createDefaults = [
            '_controller' => 'sylius.controller.product:createAction',
            '_sylius' => [
                'section'    => 'admin',
                'permission' => false,
            ],
        ];
        $routeFactory->createRoute('/products/new', $createDefaults, [], [], '', [], ['GET', 'POST'])->willReturn($createRoute);
        $routeCollection->add('sylius_admin_product_create', $createRoute)->shouldBeCalled();

        $updateDefaults = [
            '_controller' => 'sylius.controller.product:updateAction',
            '_sylius' => [
                'section'    => 'admin',
                'permission' => false,
            ],
        ];
        $routeFactory->createRoute('/products/{id}/edit', $updateDefaults, [], [], '', [], ['GET', 'PUT', 'PATCH'])->willReturn($updateRoute);
        $routeCollection->add('sylius_admin_product_update', $updateRoute)->shouldBeCalled();

        $deleteDefaults = [
            '_controller' => 'sylius.controller.product:deleteAction',
            '_sylius' => [
                'section'    => 'admin',
                'permission' => false,
            ],
        ];
        $routeFactory->createRoute('/products/{id}', $deleteDefaults, [], [], '', [], ['DELETE'])->willReturn($deleteRoute);
        $routeCollection->add('sylius_admin_product_delete', $deleteRoute)->shouldBeCalled();

        $this->load($configuration, 'sylius.resource')->shouldReturn($routeCollection);
    }

    function it_generates_routing_with_custom_templates_namespace(
        RegistryInterface $resourceRegistry,
        MetadataInterface $metadata,
        RouteFactoryInterface $routeFactory,
        RouteCollection $routeCollection,
        Route $showRoute,
        Route $indexRoute,
        Route $createRoute,
        Route $updateRoute,
        Route $deleteRoute
    ) {
        $resourceRegistry->get('sylius.product')->willReturn($metadata);
        $metadata->getApplicationName()->willReturn('sylius');
        $metadata->getName()->willReturn('product');
        $metadata->getPluralName()->willReturn('products');
        $metadata->getServiceId('controller')->willReturn('sylius.controller.product');

        $routeFactory->createRouteCollection()->willReturn($routeCollection);

        $configuration =
<<<EOT
alias: sylius.product
templates: SyliusAdminBundle:Product
EOT;

        $showDefaults = [
            '_controller' => 'sylius.controller.product:showAction',
            '_sylius' => [
                'template'   => 'SyliusAdminBundle:Product:show.html.twig',
                'permission' => false,
            ],
        ];
        $routeFactory->createRoute('/products/{id}', $showDefaults, [], [], '', [], ['GET'])->willReturn($showRoute);
        $routeCollection->add('sylius_product_show', $showRoute)->shouldBeCalled();

        $indexDefaults = [
            '_controller' => 'sylius.controller.product:indexAction',
            '_sylius' => [
                'template'   => 'SyliusAdminBundle:Product:index.html.twig',
                'permission' => false,
            ],
        ];
        $routeFactory->createRoute('/products/', $indexDefaults, [], [], '', [], ['GET'])->willReturn($indexRoute);
        $routeCollection->add('sylius_product_index', $indexRoute)->shouldBeCalled();

        $createDefaults = [
            '_controller' => 'sylius.controller.product:createAction',
            '_sylius' => [
                'template'   => 'SyliusAdminBundle:Product:create.html.twig',
                'permission' => false,
            ],
        ];
        $routeFactory->createRoute('/products/new', $createDefaults, [], [], '', [], ['GET', 'POST'])->willReturn($createRoute);
        $routeCollection->add('sylius_product_create', $createRoute)->shouldBeCalled();

        $updateDefaults = [
            '_controller' => 'sylius.controller.product:updateAction',
            '_sylius' => [
                'template'   => 'SyliusAdminBundle:Product:update.html.twig',
                'permission' => false,
            ],
        ];
        $routeFactory->createRoute('/products/{id}/edit', $updateDefaults, [], [], '', [], ['GET', 'PUT', 'PATCH'])->willReturn($updateRoute);
        $routeCollection->add('sylius_product_update', $updateRoute)->shouldBeCalled();

        $deleteDefaults = [
            '_controller' => 'sylius.controller.product:deleteAction',
            '_sylius'     => [
                'permission' => false,
            ],
        ];
        $routeFactory->createRoute('/products/{id}', $deleteDefaults, [], [], '', [], ['DELETE'])->willReturn($deleteRoute);
        $routeCollection->add('sylius_product_delete', $deleteRoute)->shouldBeCalled();

        $this->load($configuration, 'sylius.resource')->shouldReturn($routeCollection);
    }

    function it_excludes_specific_routes_if_configured(
        RegistryInterface $resourceRegistry,
        MetadataInterface $metadata,
        RouteFactoryInterface $routeFactory,
        RouteCollection $routeCollection,
        Route $indexRoute,
        Route $createRoute,
        Route $updateRoute
    ) {
        $resourceRegistry->get('sylius.product')->willReturn($metadata);
        $metadata->getApplicationName()->willReturn('sylius');
        $metadata->getName()->willReturn('product');
        $metadata->getPluralName()->willReturn('products');
        $metadata->getServiceId('controller')->willReturn('sylius.controller.product');

        $routeFactory->createRouteCollection()->willReturn($routeCollection);

        $configuration =
<<<EOT
alias: sylius.product
except: ['show', 'delete']
EOT;

        $indexDefaults = [
            '_controller' => 'sylius.controller.product:indexAction',
            '_sylius'     => [
                'permission' => false,
            ],
        ];
        $routeFactory->createRoute('/products/', $indexDefaults, [], [], '', [], ['GET'])->willReturn($indexRoute);
        $routeCollection->add('sylius_product_index', $indexRoute)->shouldBeCalled();

        $createDefaults = [
            '_controller' => 'sylius.controller.product:createAction',
            '_sylius'     => [
                'permission' => false,
            ],
        ];
        $routeFactory->createRoute('/products/new', $createDefaults, [], [], '', [], ['GET', 'POST'])->willReturn($createRoute);
        $routeCollection->add('sylius_product_create', $createRoute)->shouldBeCalled();

        $updateDefaults = [
            '_controller' => 'sylius.controller.product:updateAction',
            '_sylius'     => [
                'permission' => false,
            ],
        ];
        $routeFactory->createRoute('/products/{id}/edit', $updateDefaults, [], [], '', [], ['GET', 'PUT', 'PATCH'])->willReturn($updateRoute);
        $routeCollection->add('sylius_product_update', $updateRoute)->shouldBeCalled();

        $this->load($configuration, 'sylius.resource')->shouldReturn($routeCollection);
    }

    function it_includes_only_specific_routes_if_configured(
        RegistryInterface $resourceRegistry,
        MetadataInterface $metadata,
        RouteFactoryInterface $routeFactory,
        RouteCollection $routeCollection,
        Route $indexRoute,
        Route $createRoute
    ) {
        $resourceRegistry->get('sylius.product')->willReturn($metadata);
        $metadata->getApplicationName()->willReturn('sylius');
        $metadata->getName()->willReturn('product');
        $metadata->getPluralName()->willReturn('products');
        $metadata->getServiceId('controller')->willReturn('sylius.controller.product');

        $routeFactory->createRouteCollection()->willReturn($routeCollection);

        $configuration =
<<<EOT
alias: sylius.product
only: ['create', 'index']
EOT;

        $indexDefaults = [
            '_controller' => 'sylius.controller.product:indexAction',
            '_sylius'     => [
                'permission' => false,
            ],
        ];
        $routeFactory->createRoute('/products/', $indexDefaults, [], [], '', [], ['GET'])->willReturn($indexRoute);
        $routeCollection->add('sylius_product_index', $indexRoute)->shouldBeCalled();

        $createDefaults = [
            '_controller' => 'sylius.controller.product:createAction',
            '_sylius'     => [
                'permission' => false,
            ],
        ];
        $routeFactory->createRoute('/products/new', $createDefaults, [], [], '', [], ['GET', 'POST'])->willReturn($createRoute);
        $routeCollection->add('sylius_product_create', $createRoute)->shouldBeCalled();

        $this->load($configuration, 'sylius.resource')->shouldReturn($routeCollection);
    }

    function it_throws_an_exception_if_both_excluded_and_includes_routes_configured()
    {
        $configuration =
<<<EOT
alias: sylius.product
except: ['show', 'delete']
only: ['create']
EOT;

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('load', [$configuration, 'sylius.resource']);
    }

    function it_generates_routing_with_custom_redirect_if_specified(
        RegistryInterface $resourceRegistry,
        MetadataInterface $metadata,
        RouteFactoryInterface $routeFactory,
        RouteCollection $routeCollection,
        Route $showRoute,
        Route $indexRoute,
        Route $createRoute,
        Route $updateRoute,
        Route $deleteRoute
    ) {
        $resourceRegistry->get('sylius.product')->willReturn($metadata);
        $metadata->getApplicationName()->willReturn('sylius');
        $metadata->getName()->willReturn('product');
        $metadata->getPluralName()->willReturn('products');
        $metadata->getServiceId('controller')->willReturn('sylius.controller.product');

        $routeFactory->createRouteCollection()->willReturn($routeCollection);

        $configuration =
<<<EOT
alias: sylius.product
redirect: update
EOT;

        $showDefaults = [
            '_controller' => 'sylius.controller.product:showAction',
            '_sylius'     => [
                'permission' => false,
            ],
        ];
        $routeFactory->createRoute('/products/{id}', $showDefaults, [], [], '', [], ['GET'])->willReturn($showRoute);
        $routeCollection->add('sylius_product_show', $showRoute)->shouldBeCalled();

        $indexDefaults = [
            '_controller' => 'sylius.controller.product:indexAction',
            '_sylius'     => [
                'permission' => false,
            ],
        ];
        $routeFactory->createRoute('/products/', $indexDefaults, [], [], '', [], ['GET'])->willReturn($indexRoute);
        $routeCollection->add('sylius_product_index', $indexRoute)->shouldBeCalled();

        $createDefaults = [
            '_controller' => 'sylius.controller.product:createAction',
            '_sylius' => [
                'redirect'   => 'sylius_product_update',
                'permission' => false,
            ],
        ];
        $routeFactory->createRoute('/products/new', $createDefaults, [], [], '', [], ['GET', 'POST'])->willReturn($createRoute);
        $routeCollection->add('sylius_product_create', $createRoute)->shouldBeCalled();

        $updateDefaults = [
            '_controller' => 'sylius.controller.product:updateAction',
            '_sylius' => [
                'redirect'   => 'sylius_product_update',
                'permission' => false,
            ],
        ];
        $routeFactory->createRoute('/products/{id}/edit', $updateDefaults, [], [], '', [], ['GET', 'PUT', 'PATCH'])->willReturn($updateRoute);
        $routeCollection->add('sylius_product_update', $updateRoute)->shouldBeCalled();

        $deleteDefaults = [
            '_controller' => 'sylius.controller.product:deleteAction',
            '_sylius'     => [
                'permission' => false,
            ],
        ];
        $routeFactory->createRoute('/products/{id}', $deleteDefaults, [], [], '', [], ['DELETE'])->willReturn($deleteRoute);
        $routeCollection->add('sylius_product_delete', $deleteRoute)->shouldBeCalled();

        $this->load($configuration, 'sylius.resource')->shouldReturn($routeCollection);
    }

    function it_generates_api_routing_based_on_resource_configuration(
        RegistryInterface $resourceRegistry,
        MetadataInterface $metadata,
        RouteFactoryInterface $routeFactory,
        RouteCollection $routeCollection,
        Route $showRoute,
        Route $indexRoute,
        Route $createRoute,
        Route $updateRoute,
        Route $deleteRoute
    ) {
        $resourceRegistry->get('sylius.product')->willReturn($metadata);
        $metadata->getApplicationName()->willReturn('sylius');
        $metadata->getName()->willReturn('product');
        $metadata->getPluralName()->willReturn('products');
        $metadata->getServiceId('controller')->willReturn('sylius.controller.product');

        $routeFactory->createRouteCollection()->willReturn($routeCollection);

        $configuration =
<<<EOT
alias: sylius.product
EOT;

        $showDefaults = [
            '_controller' => 'sylius.controller.product:showAction',
            '_sylius' => [
                'serialization_groups' => ['Default', 'Detailed'],
                'permission'           => false,
            ],
        ];
        $routeFactory->createRoute('/products/{id}', $showDefaults, [], [], '', [], ['GET'])->willReturn($showRoute);
        $routeCollection->add('sylius_product_show', $showRoute)->shouldBeCalled();

        $indexDefaults = [
            '_controller' => 'sylius.controller.product:indexAction',
            '_sylius' => [
                'serialization_groups' => ['Default'],
                'permission'           => false,
            ],
        ];
        $routeFactory->createRoute('/products/', $indexDefaults, [], [], '', [], ['GET'])->willReturn($indexRoute);
        $routeCollection->add('sylius_product_index', $indexRoute)->shouldBeCalled();

        $createDefaults = [
            '_controller' => 'sylius.controller.product:createAction',
            '_sylius' => [
                'serialization_groups' => ['Default', 'Detailed'],
                'permission'           => false,
            ],
        ];
        $routeFactory->createRoute('/products/', $createDefaults, [], [], '', [], ['POST'])->willReturn($createRoute);
        $routeCollection->add('sylius_product_create', $createRoute)->shouldBeCalled();

        $updateDefaults = [
            '_controller' => 'sylius.controller.product:updateAction',
            '_sylius' => [
                'serialization_groups' => ['Default', 'Detailed'],
                'permission'           => false,
            ],
        ];
        $routeFactory->createRoute('/products/{id}', $updateDefaults, [], [], '', [], ['PUT', 'PATCH'])->willReturn($updateRoute);
        $routeCollection->add('sylius_product_update', $updateRoute)->shouldBeCalled();

        $deleteDefaults = [
            '_controller' => 'sylius.controller.product:deleteAction',
            '_sylius'     => [
                'permission' => false,
                'csrf_protection' => false
            ],
        ];
        $routeFactory->createRoute('/products/{id}', $deleteDefaults, [], [], '', [], ['DELETE'])->willReturn($deleteRoute);
        $routeCollection->add('sylius_product_delete', $deleteRoute)->shouldBeCalled();

        $this->load($configuration, 'sylius.resource_api')->shouldReturn($routeCollection);
    }

    function it_configures_grid_for_index_action_if_specified(
        RegistryInterface $resourceRegistry,
        MetadataInterface $metadata,
        RouteFactoryInterface $routeFactory,
        RouteCollection $routeCollection,
        Route $indexRoute,
        Route $createRoute
    ) {
        $resourceRegistry->get('sylius.product')->willReturn($metadata);
        $metadata->getApplicationName()->willReturn('sylius');
        $metadata->getName()->willReturn('product');
        $metadata->getPluralName()->willReturn('products');
        $metadata->getServiceId('controller')->willReturn('sylius.controller.product');

        $routeFactory->createRouteCollection()->willReturn($routeCollection);

        $configuration =
<<<EOT
alias: sylius.product
only: ['create', 'index']
grid: sylius_admin_product
EOT;

        $indexDefaults = [
            '_controller' => 'sylius.controller.product:indexAction',
            '_sylius' => [
                'grid'       => 'sylius_admin_product',
                'permission' => false,
            ]
        ];
        $routeFactory->createRoute('/products/', $indexDefaults, [], [], '', [], ['GET'])->willReturn($indexRoute);
        $routeCollection->add('sylius_product_index', $indexRoute)->shouldBeCalled();

        $createDefaults = [
            '_controller' => 'sylius.controller.product:createAction',
            '_sylius'     => [
                'permission' => false,
            ],
        ];
        $routeFactory->createRoute('/products/new', $createDefaults, [], [], '', [], ['GET', 'POST'])->willReturn($createRoute);
        $routeCollection->add('sylius_product_create', $createRoute)->shouldBeCalled();

        $this->load($configuration, 'sylius.resource')->shouldReturn($routeCollection);
    }

    function it_generates_routing_with_custom_variables(
        RegistryInterface $resourceRegistry,
        MetadataInterface $metadata,
        RouteFactoryInterface $routeFactory,
        RouteCollection $routeCollection,
        Route $showRoute,
        Route $indexRoute,
        Route $createRoute,
        Route $updateRoute,
        Route $deleteRoute
    ) {
        $resourceRegistry->get('sylius.product')->willReturn($metadata);
        $metadata->getApplicationName()->willReturn('sylius');
        $metadata->getName()->willReturn('product');
        $metadata->getPluralName()->willReturn('products');
        $metadata->getServiceId('controller')->willReturn('sylius.controller.product');

        $routeFactory->createRouteCollection()->willReturn($routeCollection);

        $configuration =
<<<EOT
alias: sylius.product
vars:
    all:
        foo: bar
    create:
        bar: foo
    update:
        abc: xyz
EOT;

        $showDefaults = [
            '_controller' => 'sylius.controller.product:showAction',
            '_sylius' => [
                'permission' => false,
                'vars'       => [
                    'foo' => 'bar',
                ]
            ],
        ];
        $routeFactory->createRoute('/products/{id}', $showDefaults, [], [], '', [], ['GET'])->willReturn($showRoute);
        $routeCollection->add('sylius_product_show', $showRoute)->shouldBeCalled();

        $indexDefaults = [
            '_controller' => 'sylius.controller.product:indexAction',
            '_sylius' => [
                'permission' => false,
                'vars'       => [
                    'foo' => 'bar',
                ]
            ],
        ];
        $routeFactory->createRoute('/products/', $indexDefaults, [], [], '', [], ['GET'])->willReturn($indexRoute);
        $routeCollection->add('sylius_product_index', $indexRoute)->shouldBeCalled();

        $createDefaults = [
            '_controller' => 'sylius.controller.product:createAction',
            '_sylius' => [
                'permission' => false,
                'vars'       => [
                    'foo' => 'bar',
                    'bar' => 'foo',
                ]
            ],
        ];
        $routeFactory->createRoute('/products/new', $createDefaults, [], [], '', [], ['GET', 'POST'])->willReturn($createRoute);
        $routeCollection->add('sylius_product_create', $createRoute)->shouldBeCalled();

        $updateDefaults = [
            '_controller' => 'sylius.controller.product:updateAction',
            '_sylius' => [
                'permission' => false,
                'vars'       => [
                    'foo' => 'bar',
                    'abc' => 'xyz',
                ]
            ],
        ];
        $routeFactory->createRoute('/products/{id}/edit', $updateDefaults, [], [], '', [], ['GET', 'PUT', 'PATCH'])->willReturn($updateRoute);
        $routeCollection->add('sylius_product_update', $updateRoute)->shouldBeCalled();

        $deleteDefaults = [
            '_controller' => 'sylius.controller.product:deleteAction',
            '_sylius' => [
                'permission' => false,
                'vars'       => [
                    'foo' => 'bar',
                ]
            ],
        ];
        $routeFactory->createRoute('/products/{id}', $deleteDefaults, [], [], '', [], ['DELETE'])->willReturn($deleteRoute);
        $routeCollection->add('sylius_product_delete', $deleteRoute)->shouldBeCalled();

        $this->load($configuration, 'sylius.resource')->shouldReturn($routeCollection);
    }

    function it_generates_routing_with_custom_permission(
        RegistryInterface $resourceRegistry,
        MetadataInterface $metadata,
        RouteFactoryInterface $routeFactory,
        RouteCollection $routeCollection,
        Route $showRoute,
        Route $indexRoute,
        Route $createRoute,
        Route $updateRoute,
        Route $deleteRoute
    ) {
        $resourceRegistry->get('sylius.product')->willReturn($metadata);
        $metadata->getApplicationName()->willReturn('sylius');
        $metadata->getName()->willReturn('product');
        $metadata->getPluralName()->willReturn('products');
        $metadata->getServiceId('controller')->willReturn('sylius.controller.product');

        $routeFactory->createRouteCollection()->willReturn($routeCollection);

        $configuration =
<<<EOT
alias: sylius.product
permission: true
EOT;

        $showDefaults = [
            '_controller' => 'sylius.controller.product:showAction',
            '_sylius'     => [
                'permission' => true,
            ],
        ];
        $routeFactory->createRoute('/products/{id}', $showDefaults, [], [], '', [], ['GET'])->willReturn($showRoute);
        $routeCollection->add('sylius_product_show', $showRoute)->shouldBeCalled();

        $indexDefaults = [
            '_controller' => 'sylius.controller.product:indexAction',
            '_sylius'     => [
                'permission' => true,
            ],
        ];
        $routeFactory->createRoute('/products/', $indexDefaults, [], [], '', [], ['GET'])->willReturn($indexRoute);
        $routeCollection->add('sylius_product_index', $indexRoute)->shouldBeCalled();

        $createDefaults = [
            '_controller' => 'sylius.controller.product:createAction',
            '_sylius'     => [
                'permission' => true,
            ],
        ];
        $routeFactory->createRoute('/products/new', $createDefaults, [], [], '', [], ['GET', 'POST'])
                     ->willReturn($createRoute);
        $routeCollection->add('sylius_product_create', $createRoute)->shouldBeCalled();

        $updateDefaults = [
            '_controller' => 'sylius.controller.product:updateAction',
            '_sylius'     => [
                'permission' => true,
            ],
        ];
        $routeFactory->createRoute('/products/{id}/edit', $updateDefaults, [], [], '', [], ['GET', 'PUT', 'PATCH'])
                     ->willReturn($updateRoute);
        $routeCollection->add('sylius_product_update', $updateRoute)->shouldBeCalled();

        $deleteDefaults = [
            '_controller' => 'sylius.controller.product:deleteAction',
            '_sylius'     => [
                'permission' => true,
            ],
        ];
        $routeFactory->createRoute('/products/{id}', $deleteDefaults, [], [], '', [], ['DELETE'])
                     ->willReturn($deleteRoute);
        $routeCollection->add('sylius_product_delete', $deleteRoute)->shouldBeCalled();

        $this->load($configuration, 'sylius.resource')->shouldReturn($routeCollection);
    }

    function it_supports_sylius_resource_type()
    {
        $this->supports('sylius.product', 'sylius.resource')->shouldReturn(true);
        $this->supports('sylius.product', 'sylius.resource_api')->shouldReturn(true);
        $this->supports('sylius.product', 'abc')->shouldReturn(false);
    }
}
