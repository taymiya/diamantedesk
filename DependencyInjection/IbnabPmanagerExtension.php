<?php

namespace Ibnab\Bundle\PmanagerBundle\DependencyInjection;
use Oro\Component\Config\Loader\CumulativeConfigLoader;
use Oro\Component\Config\Loader\YamlCumulativeFileLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class IbnabPmanagerExtension extends Extension
{
    public function prepend(ContainerBuilder $container)
    {
        $loader = new CumulativeConfigLoader(
            'ibnab_pmanager',
            new YamlCumulativeFileLoader('Resources/config/pwhitelist.yml')
        );

        $resources = $loader->load();

        $this->populateWhitelist($container, $resources);
    }

    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        $container->setParameter('ibnab_pmanager.file', $config['file']);
        $container->setParameter('ibnab_pmanager.class', $config['class']);
        $container->setParameter('ibnab_pmanager.tcpdf', $config['tcpdf']);
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
        $loader->load('processor.yml');
        $loader->load('form.yml');
        $loader->load('actions.yml');
        //$loader->load('search.yml');
        $container->prependExtensionConfig($this->getAlias(), array_intersect_key($config, array_flip(['settings'])));
        $this->prepend($container);
    }
    private function populateWhitelist(ContainerBuilder $container, $resource)
    {
        if (count($resource) > 1) {
            throw new InvalidArgumentException('Whitelist configuration has to be defined in single file');
        }

        $config = $resource[0];
        $rules = $config->data['pwhitelist'];
        $orginalRules = $container->getParameter('diamante.distribution.whitelist.rules');
        if(isset($orginalRules['simple'])){
            
            $resultSimpleRules = array_merge($orginalRules['simple'], $rules['simple']);
            $orginalRules['simple'] = $resultSimpleRules;
        }
        if(isset($orginalRules['pattern'])){
            
            $resultPaternRules = array_merge($orginalRules['pattern'], $rules['pattern']);
            $orginalRules['pattern'] =  $resultPaternRules;
        }
        $container->setParameter('diamante.distribution.whitelist.rules', $orginalRules);
    }
    /**
     * Get alias
     *
     * @return string
     */
    public function getAlias()
    {
        return 'ibnab_pmanager';
    }

}
