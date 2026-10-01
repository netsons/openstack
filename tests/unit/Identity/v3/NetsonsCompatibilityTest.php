<?php

namespace OpenStack\Test\Identity\v3;

use PHPUnit\Framework\TestCase;
use ReflectionMethod;

class NetsonsCompatibilityTest extends TestCase
{
    /**
     * Public SDK methods supported by the Netsons fork, including inherited methods.
     *
     * @dataProvider usedMethods
     */
    public function test_netsons_method_remains_public(string $class, string $method): void
    {
        self::assertTrue(method_exists($class, $method), $class.'::'.$method.' is missing');
        self::assertTrue((new ReflectionMethod($class, $method))->isPublic());
    }

    public function usedMethods(): array
    {
        $methods = [
            \OpenStack\BlockStorage\v3\Models\QuotaSet::class => ['retrieve', 'update'],
            \OpenStack\BlockStorage\v3\Models\Snapshot::class => ['delete', 'retrieve', 'update'],
            \OpenStack\BlockStorage\v3\Models\Volume::class => ['delete', 'extend', 'getMetadata', 'retrieve', 'update'],
            \OpenStack\BlockStorage\v3\Service::class => ['createSnapshot', 'createVolume', 'getQuotaSet', 'getSnapshot', 'getVolume', 'listVolumeTypes'],
            \OpenStack\Common\Error\BadResponseError::class => ['getRequest', 'getResponse'],
            \OpenStack\Compute\v2\Models\Flavor::class => ['delete', 'retrieve'],
            \OpenStack\Compute\v2\Models\Keypair::class => ['delete', 'retrieve'],
            \OpenStack\Compute\v2\Models\QuotaSet::class => ['retrieve', 'update'],
            \OpenStack\Compute\v2\Models\Server::class => ['attachVolume', 'changePassword', 'confirmResize', 'createImage', 'delete', 'detachVolume', 'getVncConsole', 'listVolumeAttachments', 'pause', 'reboot', 'rebuild', 'resize', 'retrieve', 'start', 'stop', 'unpause', 'update'],
            \OpenStack\Compute\v2\Service::class => ['createKeypair', 'createServer', 'getFlavor', 'getKeypair', 'getQuotaSet', 'getServer', 'listFlavors', 'listKeypairs'],
            \OpenStack\Identity\v3\Models\ApplicationCredential::class => ['delete', 'retrieve'],
            \OpenStack\Identity\v3\Models\Credential::class => ['delete', 'retrieve', 'update'],
            \OpenStack\Identity\v3\Models\Domain::class => ['delete', 'retrieve', 'update'],
            \OpenStack\Identity\v3\Models\Ec2Credential::class => ['delete', 'retrieve'],
            \OpenStack\Identity\v3\Models\Endpoint::class => ['delete', 'retrieve', 'update'],
            \OpenStack\Identity\v3\Models\Group::class => ['delete', 'retrieve', 'update'],
            \OpenStack\Identity\v3\Models\Policy::class => ['delete', 'retrieve', 'update'],
            \OpenStack\Identity\v3\Models\Project::class => ['delete', 'grantUserRole', 'retrieve', 'revokeUserRole', 'update'],
            \OpenStack\Identity\v3\Models\Role::class => ['delete'],
            \OpenStack\Identity\v3\Models\Service::class => ['delete', 'retrieve', 'update'],
            \OpenStack\Identity\v3\Models\Token::class => ['retrieve'],
            \OpenStack\Identity\v3\Models\User::class => ['delete', 'retrieve', 'update'],
            \OpenStack\Identity\v3\Service::class => ['createApplicationCredential', 'createCredential', 'createDomain', 'createEc2Credential', 'createEndpoint', 'createGroup', 'createPolicy', 'createProject', 'createRole', 'createService', 'createUser', 'generateToken', 'getApplicationCredential', 'getCredential', 'getDomain', 'getEc2Credential', 'getEndpoint', 'getGroup', 'getPolicy', 'getProject', 'getService', 'getToken', 'getUser', 'listApplicationCredentials', 'listCredentials', 'listDomains', 'listEc2Credentials', 'listEndpoints', 'listGroups', 'listPolicies', 'listProjects', 'listRoles', 'listServices', 'listUsers', 'revokeToken'],
            \OpenStack\Images\v2\Models\Image::class => ['delete', 'retrieve', 'update'],
            \OpenStack\Images\v2\Service::class => ['getImage', 'listImages'],
            \OpenStack\Networking\v2\Extensions\Layer3\Models\FloatingIp::class => ['associatePort', 'delete', 'disassociatePort', 'retrieve'],
            \OpenStack\Networking\v2\Extensions\Layer3\Models\Router::class => ['addInterface', 'delete', 'removeInterface', 'retrieve', 'update'],
            \OpenStack\Networking\v2\Extensions\Layer3\Service::class => ['createFloatingIp', 'createRouter', 'getFloatingIp', 'getRouter', 'listFloatingIps', 'listRouters'],
            \OpenStack\Networking\v2\Extensions\SecurityGroups\Models\SecurityGroup::class => ['delete', 'retrieve', 'update'],
            \OpenStack\Networking\v2\Extensions\SecurityGroups\Models\SecurityGroupRule::class => ['delete', 'retrieve'],
            \OpenStack\Networking\v2\Extensions\SecurityGroups\Service::class => ['createSecurityGroup', 'createSecurityGroupRule', 'getSecurityGroup', 'getSecurityGroupRule', 'listSecurityGroupRules', 'listSecurityGroups'],
            \OpenStack\Networking\v2\Models\LoadBalancer::class => ['delete', 'retrieve', 'update'],
            \OpenStack\Networking\v2\Models\LoadBalancerHealthMonitor::class => ['delete', 'retrieve', 'update'],
            \OpenStack\Networking\v2\Models\LoadBalancerListener::class => ['delete', 'retrieve', 'update'],
            \OpenStack\Networking\v2\Models\LoadBalancerPool::class => ['delete', 'retrieve', 'update'],
            \OpenStack\Networking\v2\Models\Network::class => ['delete', 'retrieve', 'update'],
            \OpenStack\Networking\v2\Models\Port::class => ['delete', 'retrieve', 'update'],
            \OpenStack\Networking\v2\Models\Quota::class => ['retrieve', 'update'],
            \OpenStack\Networking\v2\Models\RbacPolicy::class => ['delete', 'retrieve'],
            \OpenStack\Networking\v2\Models\Subnet::class => ['delete', 'retrieve', 'update'],
            \OpenStack\Networking\v2\Service::class => ['createLoadBalancer', 'createLoadBalancerHealthMonitor', 'createLoadBalancerListener', 'createLoadBalancerPool', 'createNetwork', 'createPort', 'createRbacPolicy', 'createSubnet', 'getLoadBalancer', 'getLoadBalancerHealthMonitor', 'getLoadBalancerListener', 'getLoadBalancerPool', 'getNetwork', 'getPort', 'getQuota', 'getRbacPolicy', 'getSubnet', 'listLoadBalancerHealthMonitors', 'listLoadBalancerListeners', 'listLoadBalancerPools', 'listLoadBalancers', 'listNetworks', 'listPorts', 'listRbacPolicies', 'listSubnets'],
            \OpenStack\ObjectStore\v1\Models\Container::class => ['delete', 'listObjects', 'resetMetadata', 'retrieve'],
            \OpenStack\ObjectStore\v1\Service::class => ['createContainer', 'getContainer', 'listContainers'],
            \OpenStack\OpenStack::class => ['blockStorageV3', 'computeV2', 'networkingV2'],
        ];
        $cases = [];
        foreach ($methods as $class => $names) {
            foreach ($names as $method) {
                $cases[$class.'::'.$method] = [$class, $method];
            }
        }

        return $cases;
    }
}
