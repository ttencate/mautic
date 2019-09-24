<?php

declare(strict_types=1);

/*
 * @copyright   2019 Mautic, Inc. All rights reserved
 * @author      Mautic, Inc.
 *
 * @link        https://mautic.com
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\IntegrationsBundle\Tests\Auth\Persistence;

use MauticPlugin\IntegrationsBundle\Auth\Support\Oauth2\Token\IntegrationToken;
use MauticPlugin\IntegrationsBundle\Auth\Support\Oauth2\Token\IntegrationTokenFactory;

class IntegrationTokenFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testTokenGeneratedWithExpires()
    {
        $factory = new IntegrationTokenFactory();
        $data    = [
            'access_token'  => '123',
            'refresh_token' => '456',
            'expires_in'    => 10,
        ];

        $token = $factory($data);

        $this->assertEquals($data['access_token'], $token->getAccessToken());
        $this->assertEquals($data['refresh_token'], $token->getRefreshToken());
        $this->assertFalse($token->isExpired());
    }

    public function testTokenGeneratedWithExpiresAt()
    {
        $factory = new IntegrationTokenFactory();
        $data    = [
            'access_token'  => '123',
            'refresh_token' => '456',
            'expires_at'    => 10,
        ];

        $token = $factory($data);

        $this->assertEquals($data['access_token'], $token->getAccessToken());
        $this->assertEquals($data['refresh_token'], $token->getRefreshToken());
        $this->assertFalse($token->isExpired());
    }

    public function testTokenGeneratedWithPreviousRefreshToken()
    {
        $factory = new IntegrationTokenFactory();
        $data    = [
            'access_token' => '123',
            'expires_at'   => 10,
        ];

        $previousToken = new IntegrationToken('789', '456');
        $token         = $factory($data, $previousToken);

        $this->assertEquals($data['access_token'], $token->getAccessToken());
        $this->assertEquals($previousToken->getRefreshToken(), $token->getRefreshToken());
        $this->assertFalse($token->isExpired());
    }

    public function testTokenGeneratedWithExtraData()
    {
        $factory = new IntegrationTokenFactory(['foo']);
        $data    = [
            'access_token'  => '123',
            'refresh_token' => '456',
            'expires_at'    => 10,
            'foo'           => 'bar',
            'bar'           => 'foo',
        ];

        $token = $factory($data);

        $this->assertEquals($data['access_token'], $token->getAccessToken());
        $this->assertEquals($data['refresh_token'], $token->getRefreshToken());
        $this->assertFalse($token->isExpired());
        $this->assertEquals(['foo' => 'bar'], $token->getExtraData());
    }
}