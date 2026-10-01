<?php

namespace OpenStack\Test\Identity\v3;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use OpenStack\Identity\v3\Api;
use OpenStack\Identity\v3\Models\ApplicationCredential;
use OpenStack\Identity\v3\Models\Ec2Credential;
use OpenStack\Identity\v3\Service;
use PHPUnit\Framework\TestCase;

class ForkCredentialTest extends TestCase
{
    private $history = [];

    private function service(array $responses): Service
    {
        $handler = HandlerStack::create(new MockHandler($responses));
        $handler->push(Middleware::history($this->history));

        return new Service(new Client(['handler' => $handler]), new Api());
    }

    private function response(array $data): Response
    {
        return new Response(200, ['Content-Type' => 'application/json'], json_encode($data));
    }

    public function test_ec2_credential_lifecycle(): void
    {
        $data = ['access' => 'access', 'secret' => 'secret', 'user_id' => 'user', 'tenant_id' => 'tenant'];
        $service = $this->service([
            $this->response(['credential' => $data]),
            $this->response(['credentials' => [$data]]),
            $this->response(['credential' => $data]),
            new Response(204),
        ]);

        $created = $service->createEc2Credential(['userId' => 'user', 'tenantId' => 'tenant']);
        self::assertInstanceOf(Ec2Credential::class, $created);
        self::assertSame('access', $created->access);
        self::assertSame('secret', $created->secret);
        self::assertSame('user', $created->userId);
        self::assertSame('tenant', $created->tenantId);
        self::assertSame('POST', $this->history[0]['request']->getMethod());
        self::assertSame('users/user/credentials/OS-EC2', $this->history[0]['request']->getUri()->getPath());
        self::assertSame(['tenant_id' => 'tenant'], json_decode((string) $this->history[0]['request']->getBody(), true));

        $listed = iterator_to_array($service->listEc2Credentials(['userId' => 'user']));
        self::assertCount(1, $listed);
        self::assertInstanceOf(Ec2Credential::class, $listed[0]);
        self::assertSame('access', $listed[0]->access);
        self::assertSame('GET', $this->history[1]['request']->getMethod());
        self::assertSame('users/user/credentials/OS-EC2', $this->history[1]['request']->getUri()->getPath());

        $credential = $service->getEc2Credential('user', 'access');
        self::assertCount(2, $this->history);
        $credential->retrieve();
        self::assertSame('secret', $credential->secret);
        self::assertSame('GET', $this->history[2]['request']->getMethod());
        self::assertSame('users/user/credentials/OS-EC2/access', $this->history[2]['request']->getUri()->getPath());
        $credential->delete();
        self::assertSame('DELETE', $this->history[3]['request']->getMethod());
        self::assertSame('users/user/credentials/OS-EC2/access', $this->history[3]['request']->getUri()->getPath());
    }

    public function test_application_credential_lifecycle_and_fork_fields(): void
    {
        $rules = [['path' => '/v2.1/servers', 'method' => 'GET', 'service' => 'compute']];
        $data = [
            'id' => 'credential', 'name' => 'monitoring', 'secret' => 'secret',
            'user_id' => 'user', 'project_id' => 'project',
            'expires_at' => '2030-01-01T00:00:00Z', 'unrestricted' => false,
            'access_rules' => $rules,
        ];
        $service = $this->service([
            $this->response(['application_credential' => $data]),
            $this->response(['application_credentials' => [$data]]),
            $this->response(['application_credential' => $data]),
            new Response(204),
        ]);

        $created = $service->createApplicationCredential([
            'userId' => 'user', 'name' => 'monitoring', 'access_rules' => $rules,
        ]);
        self::assertInstanceOf(ApplicationCredential::class, $created);
        self::assertSame('project', $created->projectId);
        self::assertSame('2030-01-01T00:00:00Z', $created->expiresAt);
        self::assertFalse($created->unrestricted);
        self::assertSame($rules, $created->accessRules);
        self::assertSame('POST', $this->history[0]['request']->getMethod());
        self::assertSame('users/user/application_credentials', $this->history[0]['request']->getUri()->getPath());
        self::assertSame(['application_credential' => ['name' => 'monitoring', 'access_rules' => $rules]], json_decode((string) $this->history[0]['request']->getBody(), true));

        $listed = iterator_to_array($service->listApplicationCredentials(['userId' => 'user']));
        self::assertCount(1, $listed);
        self::assertInstanceOf(ApplicationCredential::class, $listed[0]);
        self::assertSame($rules, $listed[0]->accessRules);
        self::assertSame('GET', $this->history[1]['request']->getMethod());
        self::assertSame('users/user/application_credentials', $this->history[1]['request']->getUri()->getPath());

        $credential = $service->getApplicationCredential('user', 'credential');
        self::assertCount(2, $this->history);
        $credential->retrieve();
        self::assertSame('project', $credential->projectId);
        self::assertSame('GET', $this->history[2]['request']->getMethod());
        self::assertSame('users/user/application_credentials/credential', $this->history[2]['request']->getUri()->getPath());
        $credential->delete();
        self::assertSame('DELETE', $this->history[3]['request']->getMethod());
        self::assertSame('users/user/application_credentials/credential', $this->history[3]['request']->getUri()->getPath());
    }

    public function test_credentials_can_be_filtered_by_user(): void
    {
        $service = $this->service([$this->response(['credentials' => []])]);
        self::assertSame([], iterator_to_array($service->listCredentials(['userId' => 'user'])));
        self::assertSame('credentials', $this->history[0]['request']->getUri()->getPath());
        self::assertSame('user_id=user', $this->history[0]['request']->getUri()->getQuery());
    }

    public function test_credentials_can_still_be_listed_without_options(): void
    {
        $service = $this->service([$this->response(['credentials' => []])]);
        self::assertSame([], iterator_to_array($service->listCredentials()));
        self::assertSame('', $this->history[0]['request']->getUri()->getQuery());
    }
}
