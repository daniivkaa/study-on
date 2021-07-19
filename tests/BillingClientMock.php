<?php


namespace App\Tests;

use App\Security\User;
use App\Service\BillingClient;


class BillingClientMock extends BillingClient
{
    public function login(array $credentials)
    {
        $user = new User();

        $user->setEmail($credentials['email']);
        $user->setApiToken("eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE2MjMxMzcyODIsImV4cCI6MTYyMzE0MDg4Miwicm9sZXMiOlsiUk9MRV9TVVBFUl9BRE1JTiIsIlJPTEVfVVNFUiJdLCJ1c2VybmFtZSI6ImFkbWluQG1haWwucnUifQ.cOVCfoEfJOJJ2wLzpLzf_isqoLZcOSGFtCcoSBzmMHvKYW80Kf4BfxTAGgXwEVSRzIQdEiemtuQePaq74pQrntyVEW5QFZk7h67gX5xpHal9mJIhVUsthMolqWoOkUtKLccL4hriRDEe2dTrdaEqQuoQy_b0xnGCphxgYa3PQV77iETjwgVLCMnHL3lEx0ONd1VnhD4FANVM-pGNDsAw2KGdJbaW-BQ0fgfXwrwsITwWqzOrt5nQ4YYW7CdAJ7WBed4jyFeFVxcnU2CxeXJ0GVYPw8sZxMLsXfuT4DO9UwAYQeTX76YIH_6efMPY9z1anCyWPtGLRbOZl33SwJPHGylSQ2VW26DBvctEt_LhXitHEpiNb0cpJOsDVC5NKIeRt1LG3YCq00BNKnWYbcpGb6Gv0VFFwCe-pSVKZzAH5AwZC5OTaAwJ6JsBaFP-zpsAp4Lwm5qiYO4RhHxLfU-a4AAC2e7eKXUQT4RqxvATNk8TC_ct7ZitQxv-QZQR47ak1d1u1cjhCX_F-qzYSSDwYCSThfSBWcqBvKQ_Zhesut_VnEm5rYu0Qbk4Hq3nL6_AZ8pf0olVEUET90o1828kpy2ceqkdVfov7-Ep6-pWiosIoUsYkFmTMZ8lMO8n6a2x5srilp2zwA9mWra3UAfwBkeKFcUCNpsdKL8H-10CMTA");
        $roleAdmin[] = 'ROLE_SUPER_ADMIN';
        $roleUser[] = 'ROLE_USER';

        if (str_contains($credentials['email'], 'admin')) {
            $user->setRoles($roleAdmin);
        }
        else{
            $user->setRoles($roleUser);
        }

        return $user;
    }

    public function register(array $credentials, User $user)
    {

        $user = new User();

        $user->setEmail($credentials['email']);
        $user->setApiToken("eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE2MjMxMzcyODIsImV4cCI6MTYyMzE0MDg4Miwicm9sZXMiOlsiUk9MRV9TVVBFUl9BRE1JTiIsIlJPTEVfVVNFUiJdLCJ1c2VybmFtZSI6ImFkbWluQG1haWwucnUifQ.cOVCfoEfJOJJ2wLzpLzf_isqoLZcOSGFtCcoSBzmMHvKYW80Kf4BfxTAGgXwEVSRzIQdEiemtuQePaq74pQrntyVEW5QFZk7h67gX5xpHal9mJIhVUsthMolqWoOkUtKLccL4hriRDEe2dTrdaEqQuoQy_b0xnGCphxgYa3PQV77iETjwgVLCMnHL3lEx0ONd1VnhD4FANVM-pGNDsAw2KGdJbaW-BQ0fgfXwrwsITwWqzOrt5nQ4YYW7CdAJ7WBed4jyFeFVxcnU2CxeXJ0GVYPw8sZxMLsXfuT4DO9UwAYQeTX76YIH_6efMPY9z1anCyWPtGLRbOZl33SwJPHGylSQ2VW26DBvctEt_LhXitHEpiNb0cpJOsDVC5NKIeRt1LG3YCq00BNKnWYbcpGb6Gv0VFFwCe-pSVKZzAH5AwZC5OTaAwJ6JsBaFP-zpsAp4Lwm5qiYO4RhHxLfU-a4AAC2e7eKXUQT4RqxvATNk8TC_ct7ZitQxv-QZQR47ak1d1u1cjhCX_F-qzYSSDwYCSThfSBWcqBvKQ_Zhesut_VnEm5rYu0Qbk4Hq3nL6_AZ8pf0olVEUET90o1828kpy2ceqkdVfov7-Ep6-pWiosIoUsYkFmTMZ8lMO8n6a2x5srilp2zwA9mWra3UAfwBkeKFcUCNpsdKL8H-10CMTA");
        $roles[] = 'ROLE_USER';
        $user->setRoles($roles);
        return $user;
    }
}