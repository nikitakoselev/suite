<?php

/**
 * This file is part of the Spryker Suite.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace PyzTest\Yves\Checkout\Process\Steps;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\ExpenseTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\ShipmentTransfer;
use Spryker\Shared\Shipment\ShipmentConstants;
use Spryker\Yves\StepEngine\Dependency\Plugin\Handler\StepHandlerPluginCollection;
use Spryker\Yves\StepEngine\Dependency\Plugin\Handler\StepHandlerPluginInterface;
use SprykerShop\Yves\CheckoutPage\CheckoutPageDependencyProvider;
use SprykerShop\Yves\CheckoutPage\Dependency\Client\CheckoutPageToCalculationClientInterface;
use SprykerShop\Yves\CheckoutPage\Dependency\Service\CheckoutPageToShipmentServiceInterface;
use SprykerShop\Yves\CheckoutPage\Process\Steps\ShipmentStep;
use Symfony\Component\HttpFoundation\Request;
use SprykerShop\Yves\CheckoutPage\Process\Steps\ShipmentStep\PostConditionCheckerWithoutMultiShipment;

/**
 * Auto-generated group annotations
 * @group PyzTest
 * @group Yves
 * @group Checkout
 * @group Process
 * @group Steps
 * @group ShipmentStepTest
 * Add your own group annotations below this line
 */
class ShipmentStepTest extends Unit
{
    /**
     * @return void
     */
    public function testShipmentStepExecuteShouldTriggerPlugins()
    {
        $shipmentPluginMock = $this->createShipmentMock();
        $shipmentPluginMock->expects($this->once())->method('addToDataClass');

        $shipmentStepHandler = new StepHandlerPluginCollection();
        $shipmentStepHandler->add($shipmentPluginMock, CheckoutPageDependencyProvider::PLUGIN_SHIPMENT_STEP_HANDLER);
        $shipmentStep = $this->createShipmentStep($shipmentStepHandler);

        $quoteTransfer = new QuoteTransfer();

        $shipmentTransfer = new ShipmentTransfer();
        $shipmentTransfer->setShipmentSelection(CheckoutPageDependencyProvider::PLUGIN_SHIPMENT_STEP_HANDLER);
        $quoteTransfer->setShipment($shipmentTransfer);

        $shipmentStep->execute($this->createRequest(), $quoteTransfer);
    }

    /**
     * @return void
     */
    public function testShipmentPostConditionsShouldReturnTrueWhenShipmentSet()
    {
        $quoteTransfer = new QuoteTransfer();
        $expenseTransfer = new ExpenseTransfer();
        $expenseTransfer->setType(ShipmentConstants::SHIPMENT_EXPENSE_TYPE);
        $quoteTransfer->addExpense($expenseTransfer);

        $shipmentStep = $this->createShipmentStep(new StepHandlerPluginCollection());

        $this->assertTrue($shipmentStep->postCondition($quoteTransfer));
    }

    /**
     * @param \Spryker\Yves\StepEngine\Dependency\Plugin\Handler\StepHandlerPluginCollection $shipmentPlugins
     *
     * @return \SprykerShop\Yves\CheckoutPage\Process\Steps\ShipmentStep
     */
    protected function createShipmentStep(StepHandlerPluginCollection $shipmentPlugins)
    {
        /**
         * @todo Update this test regarding to Split Delivery.
         */
        return new ShipmentStep(
            $this->createCalculationClientMock(),
            $shipmentPlugins,
            $this->createShipmentServiceMock(),
            CheckoutPageDependencyProvider::PLUGIN_SHIPMENT_STEP_HANDLER,
            'escape_route',
            $this->createShipmentStepStrategyResolver()
        );
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Request
     */
    protected function createRequest()
    {
        return Request::createFromGlobals();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\SprykerShop\Yves\CheckoutPage\Dependency\Client\CheckoutPageToCalculationClientInterface
     */
    protected function createCalculationClientMock()
    {
        $calculationMock = $this->getMockBuilder(CheckoutPageToCalculationClientInterface::class)->getMock();
        $calculationMock->method('recalculate')->willReturnArgument(0);

        return $calculationMock;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\SprykerShop\Yves\CheckoutPage\Dependency\Service\CheckoutPageToShipmentServiceInterface
     */
    protected function createShipmentServiceMock()
    {
        return $this->getMockBuilder(CheckoutPageToShipmentServiceInterface::class)->getMock();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\SprykerShop\Yves\CheckoutPage\Dependency\Client\CheckoutPageToCalculationClientInterface
     */
    protected function createShipmentStepStrategyResolverMock()
    {
        $mock = $this->getMockBuilder(CheckoutPageToCalculationClientInterface::class)->getMock();
        $mock->method('resolvePostCondition')->willReturnArgument(new PostConditionCheckerWithoutMultiShipment());

        return $mock;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Spryker\Yves\StepEngine\Dependency\Plugin\Handler\StepHandlerPluginInterface
     */
    protected function createShipmentMock()
    {
        return $this->getMockBuilder(StepHandlerPluginInterface::class)->getMock();
    }
}
