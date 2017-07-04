<?php

namespace Ibnab\Bundle\PmanagerBundle\Provider;

use Oro\Bundle\ConfigBundle\Config\ConfigManager;

class ConfigurationProvider
{
    const Allow_FIELD = 'ibnab_pmanager.allow';

    /**
     * @var ConfigManager
     */
    protected $configManager;

    /**
     * @param ConfigManager $configManager
     */
    public function __construct(ConfigManager $configManager)
    {
        $this->configManager = $configManager;
    }

    /**
     * @return string
     */
    public function getAllowed()
    {
        return $this->configManager->get(self::Allow_FIELD);
    }


}
