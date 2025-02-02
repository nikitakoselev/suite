<?php

/**
 * This file is part of the Spryker Suite.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Pyz\Zed\PropelOrm;

use Spryker\Zed\PropelOrm\PropelOrmDependencyProvider as SprykerPropelOrmDependencyProvider;
use Spryker\Zed\PropelReplicationCache\Communication\Plugin\PropelOrm\FindExtensionPlugin;
use Spryker\Zed\PropelReplicationCache\Communication\Plugin\PropelOrm\PostSaveExtensionPlugin;

class PropelOrmDependencyProvider extends SprykerPropelOrmDependencyProvider
{
    /**
     * @return array<\Spryker\Zed\PropelOrmExtension\Dependency\Plugin\FindExtensionPluginInterface>
     */
    protected function getFindExtensionPlugins(): array
    {
        return [
            new FindExtensionPlugin(),
        ];
    }

    /**
     * @return array<\Spryker\Zed\PropelOrmExtension\Dependency\Plugin\PostSaveExtensionPluginInterface>
     */
    protected function getPostSaveExtensionPlugins(): array
    {
        return [
            new PostSaveExtensionPlugin(),
        ];
    }
}
