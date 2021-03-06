<?php
/**
 * Shopware 5
 * Copyright (c) shopware AG
 *
 * According to our dual licensing model, this program can be used either
 * under the terms of the GNU Affero General Public License, version 3,
 * or under a proprietary license.
 *
 * The texts of the GNU Affero General Public License with an additional
 * permission and of our proprietary license can be found at and
 * in the LICENSE file you have received along with this program.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * "Shopware" is a registered trademark of shopware AG.
 * The licensing of the program under the AGPLv3 does not imply a
 * trademark license. Therefore any rights, title and interest in
 * our trademarks remain entirely with us.
 */

namespace Shopware\Tests\Functional\Components;

use PHPUnit\Framework\TestCase;
use Shopware\Components\StateTranslatorService;

class StateTranslatorServiceTest extends TestCase
{
    public function testTranslateStateNoTranslationShouldReturnDefault()
    {
        $translator = $this->getTranslator();

        $exampleData = [
            'name' => 'none',
        ];

        $exampleData = $translator->translateState(StateTranslatorService::STATE_ORDER, $exampleData);

        static::assertArraySubset([
            'name' => 'none',
            'description' => 'none',
        ], $exampleData);
    }

    public function testTranslateStateOrdersTranslation()
    {
        $translator = $this->getTranslator();

        $exampleData = [
            'name' => 'partially_delivered',
        ];

        $exampleData = $translator->translateState(StateTranslatorService::STATE_ORDER, $exampleData);

        static::assertArraySubset([
            'name' => 'partially_delivered',
            'description' => 'Partially delivered',
        ], $exampleData);
    }

    public function testTranslateStatePaymentsTranslation()
    {
        $translator = $this->getTranslator();

        // This is an orders status, so this shouldn't find any translation.
        $exampleData = [
            'name' => 'partially_delivered',
        ];

        $exampleData = $translator->translateState(StateTranslatorService::STATE_PAYMENT, $exampleData);

        static::assertArraySubset([
            'name' => 'partially_delivered',
            'description' => 'partially_delivered',
        ], $exampleData);

        $exampleData = [
            'name' => 'no_credit_approved',
        ];

        $exampleData = $translator->translateState(StateTranslatorService::STATE_PAYMENT, $exampleData);

        static::assertArraySubset([
            'name' => 'no_credit_approved',
            'description' => 'No credit approved',
        ], $exampleData);
    }

    public function testTranslateStateDifferentLocale()
    {
        // Defaults to en_GB
        $translator = $this->getTranslator();

        $originalExampleData = [
            'name' => 'partially_invoiced',
        ];

        $exampleData = $translator->translateState(StateTranslatorService::STATE_PAYMENT, $originalExampleData);

        static::assertArraySubset([
            'name' => 'partially_invoiced',
            'description' => 'Partially invoiced',
        ], $exampleData);

        $translator = $this->getTranslator('de_DE');

        $exampleData = $translator->translateState(StateTranslatorService::STATE_PAYMENT, $originalExampleData);

        static::assertArraySubset([
            'name' => 'partially_invoiced',
            'description' => 'Teilweise in Rechnung gestellt',
        ], $exampleData);
    }

    public function testTranslateStateShouldThrowException()
    {
        // Defaults to en_GB
        $translator = $this->getTranslator();

        $exampleData = [
            'name' => 'partially_invoiced',
        ];

        self::expectExceptionMessage('Invalid type \'foo\' given.');
        $translator->translateState('foo', $exampleData);
    }

    /**
     * @param string $locale
     *
     * @return StateTranslatorService
     */
    private function getTranslator($locale = 'en_GB')
    {
        $snippetManager = Shopware()->Container()->get('snippets');
        $locale = Shopware()->Models()->getRepository(\Shopware\Models\Shop\Locale::class)->findOneBy(
            [
                'locale' => $locale,
            ]
        );

        $snippetManager->setLocale($locale);

        return new StateTranslatorService($snippetManager);
    }
}
